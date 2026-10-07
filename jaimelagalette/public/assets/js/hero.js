document.addEventListener("DOMContentLoaded", () => {
    const hero = document.getElementById("hero");
    if (!hero) return;

    // 1) Fade-in global + image qui glisse
    hero.classList.add("is-loaded");

    // 2) "Texte qui s'écrit doucement" (mot par mot)
    const wordSpans = hero.querySelectorAll("h1 span");
    const delayPerWord = 200; // vitesse entre chaque mot (ms)

    wordSpans.forEach((span, index) => {
        setTimeout(() => {
            span.classList.add("is-visible");
        }, index * delayPerWord);
    });
});