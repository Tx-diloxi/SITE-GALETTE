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

    // Recolore les pixels d'un logo EN PLACE (RGBA) en orange brûlé.
    // Le fond est détecté (clair, sombre ou transparent) puis rendu transparent : seul le dessin du
    // logo est conservé. Fonction pure et autonome : elle est aussi injectée telle quelle dans un
    // Web Worker (voir obtenirWorker), pour ne pas bloquer le thread principal.
    function recolorerPixels(px, w, h, couleur) {
        // Logo déjà transparent : on garde son canal alpha
        var transparent = false;
        for (var i = 3; i < px.length; i += 4) {
            if (px[i] < 250) { transparent = true; break; }
        }

        var bg = [0, 0, 0], n = 0;
        // Logo transparent dont une grande surface opaque est d'une seule couleur (badge, pastille) :
        // cette couleur dominante est traitée comme le fond, le reste est conservé
        var fondDominant = false;
        if (transparent) {
            // 4096 cases (16 niveaux par canal) : comptage dans des tableaux typés, sans allocation
            var nb = new Uint32Array(4096), sr = new Uint32Array(4096), sg = new Uint32Array(4096), sb = new Uint32Array(4096);
            var opaques = 0, meilleurCle = -1, meilleurNb = 0;
            for (var q = 0; q < px.length; q += 4) {
                if (px[q + 3] < 250) continue;
                opaques++;
                var cle = ((px[q] >> 4) << 8) | ((px[q + 1] >> 4) << 4) | (px[q + 2] >> 4);
                nb[cle]++; sr[cle] += px[q]; sg[cle] += px[q + 1]; sb[cle] += px[q + 2];
                if (nb[cle] > meilleurNb) { meilleurNb = nb[cle]; meilleurCle = cle; }
            }
            if (meilleurCle >= 0 && meilleurNb > opaques * 0.35) {
                var dom = [sr[meilleurCle] / meilleurNb, sg[meilleurCle] / meilleurNb, sb[meilleurCle] / meilleurNb];
                // Garde : il doit rester du dessin une fois ce fond retiré (sinon le logo est d'une
                // seule couleur et on garde simplement sa silhouette)
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
            // Couleur de fond = moyenne des pixels du pourtour
            var bord = function (x, y) {
                var k = (y * w + x) * 4;
                bg[0] += px[k]; bg[1] += px[k + 1]; bg[2] += px[k + 2]; n++;
            };
            for (var x = 0; x < w; x++) { bord(x, 0); bord(x, h - 1); }
            for (var y = 1; y < h - 1; y++) { bord(0, y); bord(w - 1, y); }
            bg = [bg[0] / n, bg[1] / n, bg[2] / n];
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
            px[j] = couleur[0]; px[j + 1] = couleur[1]; px[j + 2] = couleur[2]; px[j + 3] = a;
        }
    }

    // Web Worker de recoloration (créé une seule fois). Le décodage (createImageBitmap) et le calcul
    // des pixels se font hors du thread principal ; sans Worker/OffscreenCanvas, repli sur le thread principal.
    var worker = null, requetes = {}, compteur = 0;

    function obtenirWorker() {
        if (worker !== null) return worker;
        worker = false;
        try {
            if (!window.Worker || !window.OffscreenCanvas || !window.createImageBitmap) return worker;
            var code = recolorerPixels.toString() +
                ';self.onmessage=function(e){var d=e.data;try{' +
                'var c=new OffscreenCanvas(d.w,d.h);var x=c.getContext("2d",{willReadFrequently:true});' +
                'x.drawImage(d.bitmap,0,0);var id=x.getImageData(0,0,d.w,d.h);' +
                'recolorerPixels(id.data,d.w,d.h,d.couleur);x.putImageData(id,0,0);' +
                'c.convertToBlob({type:"image/png"}).then(function(b){self.postMessage({id:d.id,blob:b})},' +
                'function(){self.postMessage({id:d.id,erreur:1})})}' +
                'catch(err){self.postMessage({id:d.id,erreur:1})}};';
            worker = new Worker(URL.createObjectURL(new Blob([code], { type: 'text/javascript' })));
            worker.onmessage = function (e) {
                var reponse = requetes[e.data.id];
                delete requetes[e.data.id];
                if (reponse) reponse(e.data);
            };
            worker.onerror = function () {
                worker = false;
                Object.keys(requetes).forEach(function (id) { requetes[id]({ erreur: 1 }); });
                requetes = {};
            };
        } catch (e) {
            worker = false;
        }
        return worker;
    }

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

        // Applique l'image recolorée à toutes les copies du logo (y compris celles déjà dupliquées)
        function appliquer(origine, url) {
            track.querySelectorAll('img').forEach(function (copie) {
                if (copie.dataset.origine === origine) {
                    copie.src = url;
                    marquerPret(copie);
                }
            });
        }

        // Repli : recoloration sur le thread principal
        function recolorerSurPlace(img, w, h) {
            try {
                var canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                var ctx = canvas.getContext('2d', { willReadFrequently: true });
                ctx.drawImage(img, 0, 0, w, h);
                var data = ctx.getImageData(0, 0, w, h);
                recolorerPixels(data.data, w, h, COULEUR);
                ctx.putImageData(data, 0, 0);
                appliquer(img.dataset.origine, canvas.toDataURL('image/png'));
            } catch (e) {
                // Image externe non lisible (CORS) : on garde le logo d'origine
                marquerCopies(img.dataset.origine);
            }
        }

        function recolorer(img) {
            // Déjà recolorée (copie ou nouvel événement load) : ne jamais retraiter une image orange
            if (img.src.indexOf('data:') === 0 || img.src.indexOf('blob:') === 0) {
                marquerCopies(img.dataset.origine);
                return;
            }
            var origine = img.dataset.origine;
            var ratio = Math.min(1, HAUTEUR_MAX / img.naturalHeight);
            var w = Math.max(1, Math.round(img.naturalWidth * ratio));
            var h = Math.max(1, Math.round(img.naturalHeight * ratio));
            var travailleur = obtenirWorker();
            if (!travailleur) {
                recolorerSurPlace(img, w, h);
                return;
            }
            createImageBitmap(img, { resizeWidth: w, resizeHeight: h, resizeQuality: 'medium' }).then(function (bitmap) {
                var id = ++compteur;
                requetes[id] = function (reponse) {
                    if (reponse.blob) {
                        appliquer(origine, URL.createObjectURL(reponse.blob));
                    } else {
                        recolorerSurPlace(img, w, h);
                    }
                };
                travailleur.postMessage({ id: id, bitmap: bitmap, w: w, h: h, couleur: COULEUR }, [bitmap]);
            }).catch(function () {
                recolorerSurPlace(img, w, h);
            });
        }

        // Chaque logo apparaît dès qu'il est recoloré ; filet de sécurité à 4 s : on montre le reste tel quel
        logos.forEach(function (img) { img.dataset.origine = img.getAttribute('src'); });
        // Un logo en chargement différé et hors écran ne se chargerait jamais : on doit pouvoir le lire.
        // Ces logos sont minuscules, le chargement immédiat ne coûte presque rien.
        logos.forEach(function (img) { img.loading = 'eager'; });
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

    // On attend la fin du chargement puis un moment de repos : recolorer des logos ne doit pas
    // retarder l'affichage du haut de page (LCP) ni bloquer l'interaction (TBT).
    function demarrer() {
        document.querySelectorAll('.carrousel-piste[data-boucle]').forEach(initialiser);
    }

    function auRepos() {
        if ('requestIdleCallback' in window) {
            requestIdleCallback(demarrer, { timeout: 2500 });
        } else {
            setTimeout(demarrer, 800);
        }
    }

    if (document.readyState === 'complete') {
        auRepos();
    } else {
        window.addEventListener('load', auRepos);
    }
})();
