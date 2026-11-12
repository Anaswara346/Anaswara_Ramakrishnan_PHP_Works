let count = 0;

// to select the HTML element

let counter = document.getElementById("count");

let increaseBtn = document.getElementById("increase");
let decreaseBtn = document.getElementById("decrease");
let resetBtn = document.getElementById("reset");
let increaseFiveBtn = document.getElementById("increasefive");
let message = document.getElementById("message");
// Increment button event 
increaseBtn.addEventListener("click", function(){
    count++;
    // counter.innerText = count;   // displays the value on the webpage
    updateCounter();
})

// Decrease button event
decreaseBtn.addEventListener("click", function() {
    // if (count > 0) {
    //     count--;
    // }
    count--;
    updateCounter();
});

// Reset
resetBtn.addEventListener("click",function(){
    count = 0;
    // counter.innerText = count;
    updateCounter();
});

increaseFiveBtn.addEventListener("click",function() {
    count += 5;
    updateCounter();
    // counter.innerText = count;
})

function updateCounter() {
    counter.innerText = count;
    if (count > 0) {
        counter.style.color = "darkgreen";
    }
    else if (count < 0){
        counter.style.color = "darkred";
    }
    else {
        counter.style.color = "black";
    }
    if (count > 0 && count % 10 === 0) {
        message.innerText = "Congrats! You reached " + count;
    }
    else {
        message.innerText = "";
    }
}


