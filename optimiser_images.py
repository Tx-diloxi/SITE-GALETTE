"""Optimise les images du site pour la performance (Lighthouse).

Pour chaque PNG/JPEG de jaimelagalette/public/assets/images :
  - réduit l'original s'il dépasse MAX_COTE pixels ou s'il est lourd (> SEUIL_ORIGINAL) ;
  - crée une version WebP à côté (monimage.png -> monimage.png.webp), servie automatiquement
    par le .htaccess aux navigateurs qui l'acceptent, sans changer les URL du site.

Usage : python optimiser_images.py        (nécessite Pillow : pip install pillow)
Le script peut être relancé : il ne touche que ce qui est à mettre à jour.
"""
import os
from PIL import Image

RACINE = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'jaimelagalette', 'public', 'assets', 'images')
MAX_COTE = 1600                 # plus grand côté conservé, en pixels
SEUIL_ORIGINAL = 300 * 1024     # on ne retouche un original que s'il pèse plus que ça
QUALITE_WEBP = 80
QUALITE_JPEG = 82
EXTENSIONS = ('.png', '.jpg', '.jpeg')


def a_transparence(img):
    return img.mode in ('RGBA', 'LA') or (img.mode == 'P' and 'transparency' in img.info)


def normaliser(img):
    """Convertit en RGB/RGBA (CMYK, palette...) et réduit à MAX_COTE."""
    if img.mode == 'CMYK':
        img = img.convert('RGB')
    elif img.mode == 'P':
        img = img.convert('RGBA' if 'transparency' in img.info else 'RGB')
    elif img.mode not in ('RGB', 'RGBA'):
        img = img.convert('RGBA' if a_transparence(img) else 'RGB')
    if max(img.size) > MAX_COTE:
        img.thumbnail((MAX_COTE, MAX_COTE), Image.LANCZOS)
    return img


def ko(n):
    return '%d Ko' % (n / 1024)


total_avant = total_apres = 0
for dossier, _, fichiers in os.walk(RACINE):
    for nom in sorted(fichiers):
        if not nom.lower().endswith(EXTENSIONS):
            continue
        chemin = os.path.join(dossier, nom)
        taille = os.path.getsize(chemin)
        with Image.open(chemin) as src:
            src.load()
            dims = src.size
            img = normaliser(src.copy())
        retouche = ''

        # 1. Original : réduit seulement s'il est lourd ou trop grand
        if taille > SEUIL_ORIGINAL or max(dims) > MAX_COTE:
            tmp = chemin + '.tmp'
            if nom.lower().endswith('.png'):
                img.save(tmp, 'PNG', optimize=True)
            else:
                img.convert('RGB').save(tmp, 'JPEG', quality=QUALITE_JPEG, optimize=True, progressive=True)
            if os.path.getsize(tmp) < taille:
                os.replace(tmp, chemin)
                retouche = 'original %s -> %s' % (ko(taille), ko(os.path.getsize(chemin)))
            else:
                os.remove(tmp)

        # 2. Version WebP (uniquement si elle est plus légère que l'original)
        webp = chemin + '.webp'
        img.save(webp, 'WEBP', quality=QUALITE_WEBP, method=6, alpha_quality=90)
        taille_orig = os.path.getsize(chemin)
        if os.path.getsize(webp) >= taille_orig:
            os.remove(webp)
            webp_info = 'webp plus lourd, ignoré'
            apres = taille_orig
        else:
            apres = os.path.getsize(webp)
            webp_info = 'webp %s' % ko(apres)

        total_avant += taille
        total_apres += apres
        if retouche or taille > 100 * 1024:
            print('%-55s %s | %s' % (os.path.relpath(chemin, RACINE), retouche or ko(taille), webp_info))

print('\nTotal servi aux navigateurs WebP : %s -> %s' % (ko(total_avant), ko(total_apres)))
