document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const inputs = form.querySelectorAll(".col-md-6, .col-md-4, .col-md-8, .col-md-12");
    const total = inputs.length;
    const step = 4; // Mostra 4 campos por vez
    let current = 0;

    const navDiv = document.createElement("div");
    navDiv.className = "col-12 text-center mt-3";

    const btnPrev = document.createElement("button");
    const btnNext = document.createElement("button");

    btnPrev.type = "button";
    btnNext.type = "button";
    btnPrev.className = "btn btn-secondary me-2";
    btnNext.className = "btn btn-primary";

    btnPrev.textContent = "← Anterior";
    btnNext.textContent = "Próximo →";

    navDiv.appendChild(btnPrev);
    navDiv.appendChild(btnNext);
    form.appendChild(navDiv);

    const btnSubmit = document.getElementById("btnSubmit");

    function showStep() {
        inputs.forEach((div, i) => {
            div.style.display = i >= current && i < current + step ? "block" : "none";
        });

        btnPrev.style.display = current === 0 ? "none" : "inline-block";
        btnNext.style.display = current + step >= total ? "none" : "inline-block";
        btnSubmit.style.display = current + step >= total ? "inline-block" : "none";
    }

    btnPrev.addEventListener("click", () => {
        current = Math.max(0, current - step);
        showStep();
    });

    btnNext.addEventListener("click", () => {
        current = Math.min(total - step, current + step);
        showStep();
    });

    showStep();
});
