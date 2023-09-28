const toggleAnswerButton = document.getElementById('toggleAnswerBtn')
const nextQuestionButton = document.getElementById('nextQuestionBtn')
const workedSolutionsContainer = document.getElementById('workedSolution')
const skillId = workedSolutionsContainer.getAttribute('data-skill-id')

let workedSolutions = []
let questionNo = 0
let answerDisplayed = false

function fetchWorkedSolutions () {
  fetch('/worked-solutions/fetch/' + skillId)
    .then(response => response.json())
    .then(data => {
      if (data.status !== 'success') {
        throw new Error(data.message)
      }
      workedSolutions = data.workedSolutions
      showQuestion(workedSolutions[questionNo])
    })
    .catch(error => { console.error(error) })
}

function showQuestion (currentQuestion) {
  workedSolutionsContainer.innerHTML = `<img src="/uploads/worked-solutions/${currentQuestion.question}" alt="Worked Example">`
}

function showNextQuestion () {
  questionNo++
  answerDisplayed = false
  if (questionNo >= workedSolutions.length) {
    questionNo = 0
  }
  showQuestion(workedSolutions[questionNo])
}

function processToggleAnswer () {
  toggleAnswer(workedSolutions[questionNo])
}
function toggleAnswer (currentQuestion) {
  if (answerDisplayed) {
    workedSolutionsContainer.innerHTML = `<img src="/uploads/worked-solutions/${currentQuestion.question}" alt="Worked Example">`
  } else {
    workedSolutionsContainer.innerHTML = `<img src="/uploads/worked-solutions/${currentQuestion.answer}" alt="Worked Example">`
  }
  answerDisplayed = !answerDisplayed
}

// display quiz after start button pressed
nextQuestionButton.addEventListener('click', showNextQuestion)
toggleAnswerButton.addEventListener('click', processToggleAnswer)
fetchWorkedSolutions()
