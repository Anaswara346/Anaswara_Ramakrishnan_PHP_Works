
const result = document.getElementById("result");
const previous = document.getElementById("previous");

function appendNumber(number) {

    if (result.value === "Error") {
        result.value = "0";
    }

    if (number === "." && result.value.split(/[+\-*/%]/).pop().includes(".")) {
        return;
    }

    if (result.value === "0" && number !== ".") {
        result.value = number;
    } else {
        result.value += number;
    }
}

function appendOperator(operator) {

    const lastChar = result.value.slice(-1);

    if ("+-*/%".includes(lastChar)) {
        result.value = result.value.slice(0, -1);
    }

    result.value += operator;
}

function clearDisplay() {
    result.value = "0";
    previous.textContent = "";
}

function deleteLast() {

    result.value = result.value.slice(0, -1);

    if (result.value === "") {
        result.value = "0";
    }
}

function calculate() {

    try {

        const expression = result.value;

        if (!/^[0-9+\-*/%. ]+$/.test(expression)) {
            throw new Error("Invalid expression");
        }

        const answer = Function('"use strict"; return (' + expression + ')')();

        if (!Number.isFinite(answer)) {
            throw new Error("Invalid calculation");
        }

        previous.textContent = expression + " =";

        result.value = Number(answer.toPrecision(12)).toString();

    } catch (error) {

        result.value = "Error";

    }
}
