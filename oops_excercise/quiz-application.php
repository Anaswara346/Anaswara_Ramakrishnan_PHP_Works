<?php 

class Question
{
    private $question;
    private $options;
    private $correctAnswer;

    public function __construct($question, $options, $correctAnswer)
    {
        $this->question = $question;
        $this->options = $options;
        $this->correctAnswer = $correctAnswer;
    }

    public function displayQuestion()
    {
        echo $this->question . "\n";
        foreach ($this->options as $key=> $option)
            {
                echo ($key + 1) . "." . $option . "\n";
            }
    }

    public function getCorrectAnswer()
    {
        return $this->correctAnswer;
    }
}

$q1 = new Question("What is PHP?",["Programming Language","Database","Operating System","Browser"],1);
$q2 = new Question(
    "What does OOP stand for?",
    ["Open Office Program", "Object Oriented Programming", "Object Operating Process", "Online Object Programming"],
    2
);

$q3 = new Question(
    "Which keyword is used to create an object in PHP?",
    ["class", "new", "object", "create"],
    2
);

$questions = [$q1, $q2, $q3];

$score = 0;
$quizCompleted = false;

do {

    echo "====== QUIZ MENU =========\n";
    echo "1. Start Quiz\n";
    echo "2. View Questions\n";
    echo "3. View Score\n";
    echo "4. Exit\n";

    $choice = (int) readline("Enter your choice: ");
    switch($choice){

        case 1: 
            $score = 0;

            foreach ($questions as $question)
            {
                $question->displayQuestion();
             do{
                $answer = (int) readline("Enter your answer: ");

                if ($answer < 1 || $answer > 4){
                    echo "Invalid answer!Please enter 1-4.\n";
                }
             }while ($answer < 1 || $answer > 4);

                if ($answer == $question->getCorrectAnswer()){
                    echo "Correct \n";
                    $score++;
                }else{
                    echo "Wrong\n";
                }
                echo "\n";
            }

            $quizCompleted = true;

            echo "Quiz completed\n";
            break;
        
        case 2:
            foreach ($questions as $question){
                $question->displayQuestion();
                echo "\n";
            }
            break;
        case 3:
            if ($quizCompleted){
            echo "Your score : ". $score . " / " . count($questions) . "\n";
            }else{
                echo "Please start the quiz first.\n";
            }
            break;

        case 4:
            echo "Thank you for playing\n";
            break;
        default:
            echo "Invalid choice\n";

    }
}while($choice != 4);


