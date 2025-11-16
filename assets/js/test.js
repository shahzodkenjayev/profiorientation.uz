// Test sahifasi JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const questions = document.querySelectorAll('.question-block');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const progressFill = document.getElementById('progressFill');
    const currentQuestionSpan = document.getElementById('currentQuestion');
    const totalQuestionsSpan = document.getElementById('totalQuestions');
    const form = document.getElementById('testForm');
    
    let currentQuestionIndex = 0;
    const totalQuestions = questions.length;
    
    totalQuestionsSpan.textContent = totalQuestions;
    
    // Show first question
    showQuestion(0);
    
    function showQuestion(index) {
        // Hide all questions
        questions.forEach(q => {
            q.style.display = 'none';
            q.classList.remove('active');
        });
        
        // Show current question
        if (questions[index]) {
            questions[index].style.display = 'block';
            questions[index].classList.add('active');
        }
        
        // Update buttons
        prevBtn.style.display = index > 0 ? 'block' : 'none';
        nextBtn.style.display = index < totalQuestions - 1 ? 'block' : 'none';
        submitBtn.style.display = index === totalQuestions - 1 ? 'block' : 'none';
        
        // Update progress
        const progress = ((index + 1) / totalQuestions) * 100;
        progressFill.style.width = progress + '%';
        currentQuestionSpan.textContent = index + 1;
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    function validateCurrentQuestion() {
        const currentQuestion = questions[currentQuestionIndex];
        const radioInputs = currentQuestion.querySelectorAll('input[type="radio"]');
        let isAnswered = false;
        
        radioInputs.forEach(input => {
            if (input.checked) {
                isAnswered = true;
            }
        });
        
        return isAnswered;
    }
    
    nextBtn.addEventListener('click', function() {
        if (validateCurrentQuestion()) {
            if (currentQuestionIndex < totalQuestions - 1) {
                currentQuestionIndex++;
                showQuestion(currentQuestionIndex);
            }
        } else {
            alert('Iltimos, javobni tanlang!');
        }
    });
    
    prevBtn.addEventListener('click', function() {
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
            showQuestion(currentQuestionIndex);
        }
    });
    
    form.addEventListener('submit', function(e) {
        // Check if all questions are answered
        let allAnswered = true;
        questions.forEach(question => {
            const radioInputs = question.querySelectorAll('input[type="radio"]');
            let questionAnswered = false;
            
            radioInputs.forEach(input => {
                if (input.checked) {
                    questionAnswered = true;
                }
            });
            
            if (!questionAnswered) {
                allAnswered = false;
            }
        });
        
        if (!allAnswered) {
            e.preventDefault();
            if (confirm('Ba\'zi savollar javobsiz qolgan. Testni yakunlashni xohlaysizmi?')) {
                return true;
            }
        }
    });
    
    // Allow keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowRight' && currentQuestionIndex < totalQuestions - 1) {
            if (validateCurrentQuestion()) {
                nextBtn.click();
            }
        } else if (e.key === 'ArrowLeft' && currentQuestionIndex > 0) {
            prevBtn.click();
        }
    });
});

