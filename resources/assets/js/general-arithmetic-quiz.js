import { Howl } from 'howler'
import confetti from 'canvas-confetti'

const startButton = document.getElementById('startBtn')
const instructions = document.getElementById('instructions')
const quizContainer = document.getElementById('quiz')
const feedbackContainer = document.getElementById('feedback')
const progressContainer = document.getElementById('progress')
const actionButton = document.getElementById('action')
const questionCount = parseInt(quizContainer.getAttribute('data-question-count'))
const crsfToken = quizContainer.getAttribute('data-crsf-token')

const CHECK_ANSWER = 1
const NEXT_QUESTION = 2
const SHOW_SUMMARY = 3
const DONE = 4

let state = CHECK_ANSWER

let questionNo = 0
let incorrectAttempts = 0
let currentAnswer
let score = 0
const quizStartTime = new Date().toISOString().slice(0, 19).replace('T', ' ')
let quizEndTime

const correctSound = new Howl({
  src: ['/sounds/correct.mp3']
})
const hundredPercentSound = new Howl({
  src: ['/sounds/success.mp3']
})

function randomInRange (min, max) {
  return Math.random() * (max - min) + min
}

function fireworks (timeInSeconds) {
  const duration = timeInSeconds * 1000
  const animationEnd = Date.now() + duration
  const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 }

  const interval = setInterval(function () {
    const timeLeft = animationEnd - Date.now()

    if (timeLeft <= 0) {
      return clearInterval(interval)
    }

    const particleCount = 50 * (timeLeft / duration)
    // since particles fall down, start a bit higher than random
    confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }))
    confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }))
  }, 250)
}

function startQuiz () {
  startButton.style.display = 'none'
  instructions.style.display = 'none'
  quizContainer.style.display = 'block'
  feedbackContainer.style.display = 'block'
  progressContainer.style.display = 'block'
  actionButton.style.display = 'block'
  askQuestion()
}

function askQuestion () {
  let n1
  let n2
  let operator
  let answer
  const op = Math.floor(Math.random() * (4)) + 1
  if (op === 1) {
    n1 = Math.floor(Math.random() * (41)) - 20
    n2 = Math.floor(Math.random() * (41)) - 20
    operator = '&plus;'
    answer = n1 + n2
  } else if (op === 2) {
    n1 = Math.floor(Math.random() * (41)) - 20
    n2 = Math.floor(Math.random() * (41)) - 20
    operator = '&minus;'
    answer = n1 - n2
  } else if (op === 3) {
    n1 = Math.floor(Math.random() * (25)) - 12
    n2 = Math.floor(Math.random() * (25)) - 12
    operator = '&times;'
    answer = n1 * n2
  } else {
    n2 = 0
    while (n2 === 0) {
      n2 = Math.floor(Math.random() * (25)) - 12
    }
    answer = 0
    while (answer === 0) {
      answer = Math.floor(Math.random() * (25)) - 12
    }
    n1 = n2 * answer
    operator = '&divide;'
  }
  currentAnswer = answer

  const output = []
  const answers = []
  answers.push(
    `<div class="text-center form-inline mb-3">
       ${n1} ${operator} ${n2} =
       <input size="2" class="form-control-lg" type="text" inputmode="numeric" id="answer" name="answer${questionNo}">
     </div>`
  )

  output.push(
      `<p class="question_number text-right">Question ${questionNo + 1} of ${questionCount} </p>`
  )
  output.push(
       `<hr />
       <div class="answers">${answers.join('')}</div>`
  )
  quizContainer.innerHTML = output.join('')
  const answerInput = document.getElementById('answer')
  answerInput.addEventListener('input', () => { actionButton.disabled = false })
  answerInput.addEventListener('keypress', (e) => { if (e.key === 'Enter' && !actionButton.disabled) { processClick() } })
  answerInput.focus()
}

function checkAnswer () {
  const answer = parseInt(document.getElementById('answer').value)
  if (answer === currentAnswer) {
    const box = actionButton.getBoundingClientRect()
    const confettiX = (box.x + box.width / 2) / screen.width
    const confettiY = (box.y + box.height / 2) / screen.height
    confetti({
      particleCount: 100,
      spread: 70,
      startVelocity: 30,
      gravity: 1.5,
      origin: { x: confettiX, y: confettiY }
    })
    correctSound.play()
    if (incorrectAttempts === 0) {
      score = score + 1
      document.getElementById('dot' + questionNo).classList.remove('dot')
      document.getElementById('dot' + questionNo).classList.add('dotCorrect')
    }

    feedbackContainer.innerHTML = `
      <div class="alert alert-success alert-dismissible mt-2" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <h4 class="alert-heading"><i class="bi bi-check"></i> Well Done!</h4>
        <p>Keep going!</p>
      </div>`
    moveOn()
  } else {
    const answerInput = document.getElementById('answer')
    answerInput.value = ''
    actionButton.disabled = true
    let heading = 'Not quite yet...'
    incorrectAttempts++
    if (incorrectAttempts === 2) {
      heading = 'Still not correct, yet...'
    } else if (incorrectAttempts > 2) {
      heading = 'Not yet. Keep persisting!'
    }
    feedbackContainer.innerHTML = `
      <div class="alert alert-info alert-dismissible mt-2" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <h4 class="alert-heading"><i class="bi bi-repeat"></i> ${heading}</h4>
      </div>`
    answerInput.focus()
  }
}

function moveOn () {
  // Next question or show summary
  questionNo++
  if (questionNo >= questionCount) {
    quizEndTime = new Date().toISOString().slice(0, 19).replace('T', ' ')
    recordScore(score, questionCount, quizStartTime, quizEndTime)
    state = SHOW_SUMMARY
    actionButton.innerHTML = 'Show Summary'
    actionButton.disabled = false
  } else {
    state = NEXT_QUESTION
    actionButton.innerHTML = 'Next Question'
    incorrectAttempts = 0
  }
}

function processClick () {
  actionButton.disabled = true
  if (state === CHECK_ANSWER) {
    checkAnswer()
    actionButton.disabled = false
  } else if (state === NEXT_QUESTION) {
    state = CHECK_ANSWER
    actionButton.innerHTML = 'Check Answer'
    feedbackContainer.innerHTML = ''
    askQuestion()
  } else if (state === SHOW_SUMMARY) {
    const output = []
    actionButton.innerHTML = 'Back To General Arithmetic Page'
    state = DONE
    actionButton.disabled = false
    feedbackContainer.innerHTML = ''
    output.push(`
      <h1>Keep going. Keep growing.</h1>
      <p>${score}/${questionCount} correct!</p>`)
    quizContainer.innerHTML = output.join('')
    if (score === questionCount) {
      fireworks(5)
      hundredPercentSound.play()
    }
  } else if (state === DONE) {
    window.location.href = '/general-arithmetic'
  }
}

function recordScore (score, questionCount, $quizStartTime, $quizEndTime) {
  const data = 'score=' + encodeURIComponent(score) + '&questionCount=' + encodeURIComponent(questionCount) + '&quizStartTime=' + encodeURIComponent(quizStartTime) + '&quizEndTime=' + encodeURIComponent(quizEndTime) + '&crsfToken=' + encodeURIComponent(crsfToken)
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  }
  fetch('/general-arithmetic/quizzes/record-score', options)
    .then(response => response.json())
    .then(data => {
      if (data.status !== 'success') {
        throw new Error(data.message)
      }
    })
    .catch(error => { console.error(error) })
}

// display quiz after start button pressed
startButton.addEventListener('click', startQuiz)

actionButton.disabled = true
// on submit, check answer
actionButton.addEventListener('click', processClick)
