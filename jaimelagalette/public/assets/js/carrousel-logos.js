// Carrousel de logos (partenaires, labels) : défilement infini sans raccord.
// Marquage attendu : <div class="carrousel-piste" data-boucle [data-orange]>
//                      <div class="carrousel-groupe"> …logos… </div>
//                    </div>
// data-orange : recolore aussi les logos en orange brûlé ($orange-brule).
(function () {
    'use strict';

    // Évite une double initialisation si le script est inclus par plusieurs partials
    if (window.__carrouselLogos) return;
    window.__carrouselLogos = true;

    var COULEUR = [184, 72, 0]; // $orange-brule (#B84800)
    var VITESSE = 40;           // px par seconde
    var HAUTEUR_MAX = 160;      // taille de travail du canvas (2x l'affichage)

    function initialiser(track) {
        var group = track.querySelector('.carrousel-groupe');
        var section = track.closest('section') || track.parentElement;
        if (!group) return;

        var logos = Array.prototype.slice.call(group.querySelectorAll('img'));
        var orange = track.hasAttribute('data-orange');

        // (Re)construit la boucle : duplique le groupe pour remplir l'écran (+1 groupe de marge)
        // et règle le décalage sur la largeur exacte d'un groupe (espace final compris).
        function reconstruire() {
            track.querySelectorAll('.carrousel-groupe[aria-hidden]').forEach(function (c) { c.remove(); });
            var largeur = group.getBoundingClientRect().width;
            if (!largeur) return;
            var copies = Math.ceil(section.getBoundingClientRect().width / largeur) + 1;
            for (var i = 0; i < copies; i++) {
                var clone = group.cloneNode(true);
                clone.setAttribute('aria-hidden', 'true');
                clone.querySelectorAll('a').forEach(function (a) { a.setAttribute('tabindex', '-1'); });
                clone.querySelectorAll('img').forEach(function (img) { img.setAttribute('alt', ''); });
                track.appendChild(clone);
            }
            track.style.setProperty('--decalage', largeur + 'px');
            track.style.setProperty('--duree', (largeur / VITESSE) + 's');
        }

        // Recalcule dès que la largeur du groupe ou de la section change
        // (chargement des logos, recoloration, redimensionnement de la fenêtre)
        var planifie = false;
        function planifier() {
            if (planifie) return;
            planifie = true;
            setTimeout(function () { planifie = false; reconstruire(); }, 0);
        }
        if ('ResizeObserver' in window) {
            var obs = new ResizeObserver(planifier);
            obs.observe(group);
            obs.observe(section);
        } else {
            window.addEventListener('resize', planifier);
            window.addEventListener('load', planifier);
        }

        if (!orange) {
            planifier();
            return;
        }

        // ── Recoloration en orange brûlé ──
        // Un logo reste invisible tant qu'il n'est pas recoloré (pas de flash des couleurs d'origine)
        function marquerPret(img) { img.dataset.pret = '1'; }
        function marquerCopies(origine) {
            track.querySelectorAll('img').forEach(function (copie) {
                if (copie.dataset.origine === origine) marquerPret(copie);
            });
        }

        // Le fond est détecté (clair, sombre ou transparent) puis rendu transparent :
        // seul le dessin du logo est conservé, rempli en orange brûlé.
        function recolorer(img) {
            // Déjà recolorée (copie ou nouvel événement load) : ne jamais retraiter une image orange
            if (img.src.indexOf('data:') === 0) {
                marquerCopies(img.dataset.origine);
                return;
            }
            try {
                var ratio = Math.min(1, HAUTEUR_MAX / img.naturalHeight);
                var w = Math.max(1, Math.round(img.naturalWidth * ratio));
                var h = Math.max(1, Math.round(img.naturalHeight * ratio));
                var canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                var ctx = canvas.getContext('2d', { willReadFrequently: true });
                ctx.drawImage(img, 0, 0, w, h);
                var data = ctx.getImageData(0, 0, w, h);
                var px = data.data;

                // Logo déjà transparent : on garde son canal alpha tel quel
                var transparent = false;
                for (var i = 3; i < px.length; i += 4) {
                    if (px[i] < 250) { transparent = true; break; }
                }

                // Couleur de fond = moyenne des pixels du pourtour
                var bg = [0, 0, 0], n = 0;
                // Logo transparent dont une grande surface opaque est d'une seule couleur (badge,
                // pastille) : cette couleur dominante est traitée comme le fond, le reste est conservé
                var fondDominant = false;
                if (transparent) {
                    var bins = {}, opaques = 0, meilleur = null;
                    for (var q = 0; q < px.length; q += 4) {
                        if (px[q + 3] < 250) continue;
                        opaques++;
                        var cle = (px[q] >> 4) + ',' + (px[q + 1] >> 4) + ',' + (px[q + 2] >> 4);
                        var bin = bins[cle] || (bins[cle] = { n: 0, r: 0, g: 0, b: 0 });
                        bin.n++; bin.r += px[q]; bin.g += px[q + 1]; bin.b += px[q + 2];
                        if (!meilleur || bin.n > meilleur.n) meilleur = bin;
                    }
                    if (meilleur && meilleur.n > opaques * 0.35) {
                        var dom = [meilleur.r / meilleur.n, meilleur.g / meilleur.n, meilleur.b / meilleur.n];
                        // Garde : il doit rester du dessin une fois ce fond retiré (sinon le logo est
                        // d'une seule couleur et on garde simplement sa silhouette)
                        var dessin = 0;
                        for (var r = 0; r < px.length; r += 4) {
                            if (px[r + 3] < 250) continue;
                            var e = Math.sqrt(Math.pow(px[r] - dom[0], 2) + Math.pow(px[r + 1] - dom[1], 2) + Math.pow(px[r + 2] - dom[2], 2));
                            if (e > 110) dessin++;
                        }
                        if (dessin > opaques * 0.03) {
                            fondDominant = true;
                            bg = dom;
                        }
                    }
                }
                if (!transparent) {
                    var bord = function (x, y) {
                        var k = (y * w + x) * 4;
                        bg[0] += px[k]; bg[1] += px[k + 1]; bg[2] += px[k + 2]; n++;
                    };
                    for (var x = 0; x < w; x++) { bord(x, 0); bord(x, h - 1); }
                    for (var y = 1; y < h - 1; y++) { bord(0, y); bord(w - 1, y); }
                    bg = bg.map(function (v) { return v / n; });
                }

                for (var j = 0; j < px.length; j += 4) {
                    var a;
                    if (transparent && !fondDominant) {
                        a = px[j + 3];
                    } else {
                        // Opacité = écart de couleur avec le fond (plein dès la moitié de l'écart max)
                        var d = Math.sqrt(
                            Math.pow(px[j] - bg[0], 2) + Math.pow(px[j + 1] - bg[1], 2) + Math.pow(px[j + 2] - bg[2], 2)
                        );
                        a = Math.min(255, (d / 220) * 255);
                        // Logo transparent : on conserve aussi la transparence d'origine
                        if (transparent) a = a * px[j + 3] / 255;
                    }
                    px[j] = COULEUR[0]; px[j + 1] = COULEUR[1]; px[j + 2] = COULEUR[2]; px[j + 3] = a;
                }
                ctx.putImageData(data, 0, 0);

                // Met à jour toutes les copies de ce logo (y compris celles déjà dupliquées)
                var url = canvas.toDataURL('image/png');
                track.querySelectorAll('img').forEach(function (copie) {
                    if (copie.dataset.origine === img.dataset.origine) {
                        copie.src = url;
                        marquerPret(copie);
                    }
                });
            } catch (e) {
                // Image externe non lisible (CORS) : on garde le logo d'origine
                marquerCopies(img.dataset.origine);
            }
        }

        // Chaque logo apparaît dès qu'il est recoloré ; filet de sécurité à 4 s : on montre le reste tel quel
        logos.forEach(function (img) { img.dataset.origine = img.getAttribute('src'); });
        logos.forEach(function (img) {
            var traiter = function () {
                if (img.naturalWidth) {
                    recolorer(img);
                } else {
                    marquerCopies(img.dataset.origine);
                }
                planifier();
            };
            if (img.complete) {
                traiter();
            } else {
                img.addEventListener('load', traiter, { once: true });
                img.addEventListener('error', traiter, { once: true });
            }
        });
        setTimeout(function () {
            track.querySelectorAll('img').forEach(marquerPret);
            planifier();
        }, 4000);
    }

    function demarrer() {
        document.querySelectorAll('.carrousel-piste[data-boucle]').forEach(initialiser);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', demarrer);
    } else {
        demarrer();
    }
})();
