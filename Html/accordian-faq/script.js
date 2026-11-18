const questions = document.querySelectorAll(".faq-question");

questions.forEach(question => {
    question.addEventListener("click", function() {
        const currentItem = this.parentElement;
        const icon = this.querySelector("span");

        document.querySelectorAll(".faq-item")
        .forEach(item => {
            if (item !== currentItem) {
                item.classList.remove("active");

                item.querySelector("span").textContent = "+";
            }
        });

        currentItem.classList.toggle("active");

        if (currentItem.classList.contains("active")) {
            icon.textContent = "-";
        } else {
            icon.textContent = "+";
        }
    });
});