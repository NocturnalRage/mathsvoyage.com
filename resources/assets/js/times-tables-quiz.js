import { Howl } from 'howler'
import confetti from 'canvas-confetti'

const startButton = document.getElementById('startBtn')
const instructions = document.getElementById('instructions')
const quizContainer = document.getElementById('quiz')
const feedbackContainer = document.getElementById('feedback')
const progressContainer = document.getElementById('progress')
const actionButton = document.getElementById('action')
const attemptId = parseInt(quizContainer.getAttribute('data-attempt-id'))
const timesTablesId = parseInt(quizContainer.getAttribute('data-times-tables-id'))
const minNumber = parseInt(quizContainer.getAttribute('data-min-number'))
const maxNumber = parseInt(quizContainer.getAttribute('data-max-number'))
const questionCount = parseInt(quizContainer.getAttribute('data-question-count'))
const repetitions = parseInt(quizContainer.getAttribute('data-repetitions'))
const attempt = parseInt(quizContainer.getAttribute('data-attempt'))
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
  const output = []
  const answers = []
  let num1 = Math.floor(Math.random() * 13)
  let num2
  if (minNumber === maxNumber) {
    num2 = minNumber
  } else {
    num2 = Math.floor(Math.random() * (maxNumber - minNumber + 1)) + minNumber
  }
  let num3 = num1 * num2
  let symbol = 'x'
  const multiplyQuestion = Math.floor(Math.random() * 2)
  if (multiplyQuestion) {
    if (num1 !== 0) {
      const pos = Math.floor(Math.random() * 2)
      if (pos === 1) {
        const temp = num2
        num2 = num1
        num1 = temp
      }
    }
  } else {
    // Divide Question
    symbol = '&#247;'
    if (num1 !== 0) {
      const temp = num1
      num1 = num3
      num3 = temp
      const pos = Math.floor(Math.random() * 2)
      if (pos === 1) {
        const temp = num3
        num3 = num2
        num2 = temp
      }
    }
  }
  const nums = [num1, num2, num3]
  let missingPos = Math.floor(Math.random() * 3)
  if (num1 === 0 && missingPos === 1) {
    const newPos = Math.floor(Math.random() * 2)
    if (newPos === 0) {
      missingPos = 0
    } else {
      missingPos = 2
    }
  }
  currentAnswer = nums[missingPos]
  if (missingPos === 0) {
    answers.push(
          `<div class="text-center form-inline mb-3">
              <input size="2" class="form-control-lg" type="text" inputmode="numeric" id="answer" name="answer${questionNo}"> ${symbol} ${nums[1]} = ${nums[2]}
          </div>`
    )
  } else if (missingPos === 1) {
    answers.push(
          `<div class="text-center form-inline mb-3">
              ${nums[0]} ${symbol}
              <input size="2" class="form-control-lg" type="text" inputmode="numeric" id="answer" name="answer${questionNo}"> = ${nums[2]}
          </div>`
    )
  } else {
    answers.push(
          `<div class="text-center form-inline mb-3">
              ${nums[0]} ${symbol} ${nums[1]} =
              <input size="2" class="form-control-lg" type="text" inputmode="numeric" id="answer" name="answer${questionNo}">
          </div>`
    )
  }

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
    recordScore(attemptId, timesTablesId, attempt, score, quizStartTime, quizEndTime)
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
    actionButton.innerHTML = 'Back To Times Tables Page'
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
    window.location.href = '/times-tables'
  }
}

function recordScore (attemptId, timesTablesId, attempt, score, quizStartTime, quizEndTime) {
  const data = 'attemptId=' + encodeURIComponent(attemptId) + '&timesTablesId=' + encodeURIComponent(timesTablesId) + '&attempt=' + attempt + '&score=' + encodeURIComponent(score) + '&questionCount=' + encodeURIComponent(questionCount) + '&quizStartTime=' + encodeURIComponent(quizStartTime) + '&quizEndTime=' + encodeURIComponent(quizEndTime) + '&crsfToken=' + encodeURIComponent(crsfToken)
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  }
  fetch('/times-tables/quizzes/record-score', options)
    .then(response => response.json())
    .then(data => {
      if (data.status !== 'success') {
        throw new Error(data.message)
      }
    })
    .catch(error => { console.error(error) })

  if (attempt >= repetitions && timesTablesId === 17) {
    const data = 'attemptId=' + encodeURIComponent(attemptId) + '&crsfToken=' + encodeURIComponent(crsfToken)
    const options = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: data
    }
    fetch('/times-tables/quizzes/complete-attempt', options)
      .then(response => response.json())
      .then(data => {
        if (data.status !== 'success') {
          throw new Error(data.message)
        }
      })
      .catch(error => { console.error(error) })
  } else if (attempt >= repetitions) {
    const data = 'attemptId=' + encodeURIComponent(attemptId) + '&crsfToken=' + encodeURIComponent(crsfToken)
    const options = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: data
    }
    fetch('/times-tables/quizzes/increment-times-table', options)
      .then(response => response.json())
      .then(data => {
        if (data.status !== 'success') {
          throw new Error(data.message)
        }
      })
      .catch(error => { console.error(error) })
  } else {
    const data = 'attemptId=' + encodeURIComponent(attemptId) + '&crsfToken=' + encodeURIComponent(crsfToken)
    const options = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: data
    }
    fetch('/times-tables/quizzes/increment-attempt', options)
      .then(response => response.json())
      .then(data => {
        if (data.status !== 'success') {
          throw new Error(data.message)
        }
      })
      .catch(error => { console.error(error) })
  }
}

// display quiz after start button pressed
startButton.addEventListener('click', startQuiz)

actionButton.disabled = true
// on submit, check answer
actionButton.addEventListener('click', processClick)
