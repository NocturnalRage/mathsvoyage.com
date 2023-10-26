import * as KAS from './kas/index'
import { renderMathInElement } from 'mathlive'
import { Howl } from 'howler'
import confetti from 'canvas-confetti'
console.log(fractionToDecimal('-200/-3'))

const startButton = document.getElementById('startBtn')
const instructions = document.getElementById('instructions')
const quizContainer = document.getElementById('quiz')
const hintContainer = document.getElementById('hint')
const feedbackContainer = document.getElementById('feedback')
const progressContainer = document.getElementById('progress')
const actionButton = document.getElementById('action')
const crsfToken = quizContainer.getAttribute('data-crsf-token')
const quizId = quizContainer.getAttribute('data-quiz-id')
const returnSlug = quizContainer.getAttribute('data-return-slug')

const CHECK_ANSWER = 1
const NEXT_QUESTION = 2
const SHOW_SUMMARY = 3
const DONE = 4

const MULTIPLE_CHOICE = 1
const KAS_QUESTION = 2
const NUMERIC_QUESTION = 3
let state = CHECK_ANSWER

let quizOptions = []
let questionNo = 0
let totalQuestions = 0
let incorrectAttempts = 0
let hintUsed = false
let correctUnaidedTotal = 0
const quizStartTime = new Date().toISOString().slice(0, 19).replace('T', ' ')
let quizEndTime
let questionStartTime
let questionEndTime
let skillsMatrix = []

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

function fetchQuestions () {
  startButton.style.display = 'none'
  instructions.style.display = 'none'
  quizContainer.style.display = 'block'
  feedbackContainer.style.display = 'block'
  progressContainer.style.display = 'block'
  actionButton.style.display = 'block'
  // Fetch the quizOptions
  fetch('/quizzes/questions/' + quizId)
    .then(response => response.json())
    .then(data => {
      if (data.status !== 'success') {
        throw new Error(data.message)
      }
      quizOptions = data.options
      totalQuestions = quizOptions.length
      askQuestion(quizOptions[questionNo])
    })
    .catch(error => { console.error(error) })
}

function askQuestion (currentQuestion) {
  questionStartTime = new Date().toISOString().slice(0, 19).replace('T', ' ')
  if (currentQuestion.skill_question_type_id === MULTIPLE_CHOICE) {
    askMultipleChoiceQuestion(currentQuestion)
  } else if (currentQuestion.skill_question_type_id === KAS_QUESTION) {
    askKasQuestion(currentQuestion)
  } else if (currentQuestion.skill_question_type_id === NUMERIC_QUESTION) {
    askNumericQuestion(currentQuestion)
  }
  renderMathInElement(quizContainer)
}

function askMultipleChoiceQuestion (currentQuestion) {
  const output = []
  const answers = []

  output.push(quizQuestionHtml(currentQuestion))

  for (const option in currentQuestion.answers) {
    answers.push(
          `<div class="form-group">
            <label>
              <input type="radio" name="question${questionNo}" value="${currentQuestion.answers[option].skill_question_option_id}">
            ${currentQuestion.answers[option].option}
            </label>
          </div>`
    )
  }
  output.push(
       `<div class="answers">${answers.join('')}</div>
        </div><!-- quizQuestion-->`
  )
  quizContainer.innerHTML = output.join('')
  const radios = document.querySelectorAll('input[name="question' + questionNo + '"]')
  radios.forEach(radio => radio.addEventListener('change', () => { actionButton.disabled = false; actionButton.focus() }))
}

function askKasQuestion (currentQuestion) {
  const output = []
  output.push(quizQuestionHtml(currentQuestion))
  output.push('</div><!-- quizQuestion-->')
  quizContainer.innerHTML = output.join('')
  const numAnswers = currentQuestion.answers.length
  for (let answerNum = 0; answerNum < numAnswers; answerNum++) {
    const mathfield = document.getElementById('mf_answer_' + answerNum)
    if (answerNum === 0) {
      mathfield.focus()
    }
    mathfield.addEventListener('input', (ev) => {
      ev.preventDefault()
      if (isValidExpression(mathfield)) {
        mathfield.style.setProperty('border-color', 'blue')
        actionButton.disabled = false
      } else {
        mathfield.style.setProperty('border-color', 'orange')
        actionButton.disabled = true
      }
      if (!actionButton.disabled && ev.data === 'insertLineBreak') {
        processClick()
      }
    })
  }
}

function askNumericQuestion (currentQuestion) {
  const output = []
  output.push(quizQuestionHtml(currentQuestion))
  output.push('</div><!-- quizQuestion-->')
  quizContainer.innerHTML = output.join('')
  const numAnswers = currentQuestion.answers.length
  for (let answerNum = 0; answerNum < numAnswers; answerNum++) {
    const numericInput = document.getElementById('numeric_input_' + answerNum)
    if (answerNum === 0) {
      numericInput.focus()
    }
    numericInput.addEventListener('keyup', (ev) => {
      ev.preventDefault()
      if (numericInput.value !== '') {
        actionButton.disabled = false
      } else {
        actionButton.disabled = true
      }
      if (!actionButton.disabled && ev.key === 'Enter') {
        processClick()
      }
    })
  }
}

function checkAnswer (currentQuestion) {
  if (currentQuestion.skill_question_type_id === MULTIPLE_CHOICE) {
    checkMultipleChoiceAnswer(currentQuestion)
  } else if (currentQuestion.skill_question_type_id === KAS_QUESTION) {
    checkKasAnswer(currentQuestion)
  } else if (currentQuestion.skill_question_type_id === NUMERIC_QUESTION) {
    checkNumericAnswer(currentQuestion)
  }
}

function checkMultipleChoiceAnswer (currentQuestion) {
  let correctValue
  for (const option in currentQuestion.answers) {
    if (currentQuestion.answers[option].correct === 1) {
      correctValue = currentQuestion.answers[option].skill_question_option_id
    }
  }
  const radioButtons = document.querySelectorAll('input[name="question' + questionNo + '"]')
  radioButtons.forEach(element => { element.disabled = true })
  const choice = document.querySelector('input[name="question' + questionNo + '"]:checked').value

  let correctOrNot = 0
  if (parseInt(choice) === correctValue) {
    showConfetti()
    correctOrNot = correctWithoutHelp(incorrectAttempts, hintUsed)
    questionEndTime = new Date().toISOString().slice(0, 19).replace('T', ' ')
    recordResponse(
      currentQuestion.skill_question_id,
      currentQuestion.skill_question_type_id,
      choice,
      null,
      correctOrNot,
      questionStartTime,
      questionEndTime
    )

    showWellDoneFeedback()
    moveOn()
  } else {
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
        <p>Try again, <a id="hintLink" href="#">get help</a>, or <a id="skipLink" href="#">skip for now</a>.</p>
      </div>`
    const hintLink = document.getElementById('hintLink')
    hintLink.addEventListener('click', function (event) {
      event.preventDefault()
      hintUsed = true
      const output = []
      for (const hint in currentQuestion.hints) {
        output.push(
           `<h4>Hint ${parseInt(hint) + 1}</h4>
            <p>${currentQuestion.hints[hint].hint}</p>
            </div>`)
      }
      hintContainer.innerHTML = output.join('')
      hintContainer.style.display = 'block'
      renderMathInElement(hintContainer)
    })
    const skipLink = document.getElementById('skipLink')
    skipLink.addEventListener('click', function (event) {
      event.preventDefault()
      questionEndTime = new Date().toISOString().slice(0, 19).replace('T', ' ')
      recordResponse(
        currentQuestion.skill_question_id,
        currentQuestion.skill_question_type_id,
        choice,
        null,
        correctOrNot,
        questionStartTime,
        questionEndTime
      )
      moveOn()
      if (questionNo < totalQuestions) {
        processClick()
      } else {
        feedbackContainer.innerHTML = ''
      }
    })
    radioButtons.forEach(element => { element.disabled = false })
  }
}

function checkKasAnswer (currentQuestion) {
  let allAnswered = true
  const numAnswers = currentQuestion.answers.length
  for (let answerNum = 0; answerNum < numAnswers; answerNum++) {
    const mathfield = document.getElementById('mf_answer_' + answerNum)
    if (mathfield.value === '') {
      allAnswered = false
    }
  }
  if (!allAnswered) {
    feedbackContainer.innerHTML = `
      <div class="alert alert-info alert-dismissible mt-2" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <h4 class="alert-heading"><i class="bi bi-repeat"></i> Answer All Questions</h4>
        <p>Some questions are still unanswered. Make sure to answer them all before pressing enter.</p>
      </div>`
    return
  }
  let studentAnswers = ''
  let nearlyCorrect = false
  let feedbackMessage = ''
  const mathfields = document.querySelectorAll('math-field')
  mathfields.forEach(element => { element.disabled = true })
  let totalCorrect = 0
  for (let answerNum = 0; answerNum < numAnswers; answerNum++) {
    const [mathfield, studentAnswer, studentExpr] = retrieveMathfieldAnswerAndExpression(answerNum)
    const correctExpr = KAS.parse(currentQuestion.answers[answerNum].answer).expr
    const comparison = KAS.compare(studentExpr, correctExpr, { form: currentQuestion.answers[answerNum].form, simplify: currentQuestion.answers[answerNum].simplify })
    if (comparison.equal) {
      totalCorrect++
    } else if (comparison.message !== null) {
      nearlyCorrect = true
      feedbackMessage += comparison.message + ' '
    } else {
      mathfield.style.setProperty('border-color', 'red')
    }
    studentAnswers += studentAnswer + ' '
  }

  let correctOrNot = 0
  if (totalCorrect === numAnswers) {
    showConfetti()
    correctOrNot = correctWithoutHelp(incorrectAttempts, hintUsed)
    questionEndTime = new Date().toISOString().slice(0, 19).replace('T', ' ')
    recordResponse(
      currentQuestion.skill_question_id,
      currentQuestion.skill_question_type_id,
      null,
      studentAnswers,
      correctOrNot,
      questionStartTime,
      questionEndTime
    )

    showWellDoneFeedback()
    moveOn()
  } else if (!nearlyCorrect) {
    mathfields.forEach(element => { element.disabled = false })
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
        <p>Try again, <a id="hintLink" href="#">get help</a>, or <a id="skipLink" href="#">skip for now</a>.</p>
      </div>`
    const hintLink = document.getElementById('hintLink')
    hintLink.addEventListener('click', function (event) {
      event.preventDefault()
      hintUsed = true
      const output = []
      for (const hint in currentQuestion.hints) {
        output.push(
           `<h4>Hint ${parseInt(hint) + 1}</h4>
            <p>${currentQuestion.hints[hint].hint}</p>
            </div>`)
      }
      hintContainer.innerHTML = output.join('')
      hintContainer.style.display = 'block'
      renderMathInElement(hintContainer)
    })
    const skipLink = document.getElementById('skipLink')
    skipLink.addEventListener('click', function (event) {
      event.preventDefault()
      questionEndTime = new Date().toISOString().slice(0, 19).replace('T', ' ')
      recordResponse(
        currentQuestion.skill_question_id,
        currentQuestion.skill_question_type_id,
        null,
        studentAnswers,
        correctOrNot,
        questionStartTime,
        questionEndTime
      )
      moveOn()
      if (questionNo < totalQuestions) {
        processClick()
      } else {
        feedbackContainer.innerHTML = ''
      }
    })
  } else {
    mathfields.forEach(element => { element.disabled = false })
    feedbackContainer.innerHTML = `
      <div class="alert alert-info alert-dismissible mt-2" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <h4 class="alert-heading"><i class="bi bi-repeat"></i> Close...</h4>
        <p>${feedbackMessage}</p>
      </div>`
  }
}

function checkNumericAnswer (currentQuestion) {
  let allAnswered = true
  const numAnswers = currentQuestion.answers.length
  for (let answerNum = 0; answerNum < numAnswers; answerNum++) {
    const numericInput = document.getElementById('numeric_input_' + answerNum)
    if (numericInput.value === '') {
      allAnswered = false
    }
  }
  if (!allAnswered) {
    feedbackContainer.innerHTML = `
      <div class="alert alert-info alert-dismissible mt-2" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <h4 class="alert-heading"><i class="bi bi-repeat"></i> Answer All Questions</h4>
        <p>Some questions are still unanswered. Make sure to answer them all before pressing enter.</p>
      </div>`
    return
  }
  let studentAnswers = ''
  let nearlyCorrect = false
  let feedbackMessage = ''
  const numericInputs = document.querySelectorAll('.numericInput')
  numericInputs.forEach(element => { element.disabled = true })
  let totalCorrect = 0
  let correctAnswer
  for (let answerNum = 0; answerNum < numAnswers; answerNum++) {
    const solution = currentQuestion.answers[answerNum]
    const solutionAnswerType = getAnswerType(solution.answer)
    if (solutionAnswerType === 'integer') {
      correctAnswer = parseInt(solution.answer)
    } else if (solutionAnswerType === 'decimal') {
      correctAnswer = parseFloat(solution.answer)
    } else if (solutionAnswerType === 'proper' || solutionAnswerType === 'improper') {
      const solutionFrac = fractionToDecimal(solution.answer)
      correctAnswer = solutionFrac.value
    } else {
      correctAnswer = parseFloat(solution.answer)
    }
    const studentInput = document.getElementById('numeric_input_' + answerNum)
    const studentAnswer = studentInput.value
    const answerType = getAnswerType(studentAnswer)
    if (answerType === 'integer' && parseInt(studentAnswer) === correctAnswer) {
      totalCorrect++
    } else if (answerType === 'decimal' && parseFloat(studentAnswer) === correctAnswer) {
      totalCorrect++
    } else if (answerType === 'proper' || answerType === 'improper') {
      const frac = fractionToDecimal(studentAnswer)
      const parsedStudentAnswer = frac.value
      if (parsedStudentAnswer === correctAnswer) {
        if (!solution.simplify) {
          totalCorrect++
        } else if (solution.simplify && frac.simplified) {
          totalCorrect++
        } else {
          nearlyCorrect = true
          feedbackMessage += 'You need to simplify ' + studentAnswer + ' '
        }
      }
    }
    studentAnswers += studentInput.value + ' '
  }

  let correctOrNot = 0
  if (totalCorrect === numAnswers) {
    showConfetti()
    correctOrNot = correctWithoutHelp(incorrectAttempts, hintUsed)
    questionEndTime = new Date().toISOString().slice(0, 19).replace('T', ' ')
    recordResponse(
      currentQuestion.skill_question_id,
      currentQuestion.skill_question_type_id,
      null,
      studentAnswers,
      correctOrNot,
      questionStartTime,
      questionEndTime
    )

    showWellDoneFeedback()
    moveOn()
  } else if (!nearlyCorrect) {
    numericInputs.forEach(element => { element.disabled = false })
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
        <p>Try again, <a id="hintLink" href="#">get help</a>, or <a id="skipLink" href="#">skip for now</a>.</p>
      </div>`
    const hintLink = document.getElementById('hintLink')
    hintLink.addEventListener('click', function (event) {
      event.preventDefault()
      hintUsed = true
      const output = []
      for (const hint in currentQuestion.hints) {
        output.push(
           `<h4>Hint ${parseInt(hint) + 1}</h4>
            <p>${currentQuestion.hints[hint].hint}</p>
            </div>`)
      }
      hintContainer.innerHTML = output.join('')
      hintContainer.style.display = 'block'
      renderMathInElement(hintContainer)
    })
    const skipLink = document.getElementById('skipLink')
    skipLink.addEventListener('click', function (event) {
      event.preventDefault()
      questionEndTime = new Date().toISOString().slice(0, 19).replace('T', ' ')
      recordResponse(
        currentQuestion.skill_question_id,
        currentQuestion.skill_question_type_id,
        null,
        studentAnswers,
        correctOrNot,
        questionStartTime,
        questionEndTime
      )
      moveOn()
      if (questionNo < totalQuestions) {
        processClick()
      } else {
        feedbackContainer.innerHTML = ''
      }
    })
  } else {
    numericInputs.forEach(element => { element.disabled = false })
    feedbackContainer.innerHTML = `
      <div class="alert alert-info alert-dismissible mt-2" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <h4 class="alert-heading"><i class="bi bi-repeat"></i> Close...</h4>
        <p>${feedbackMessage}</p>
      </div>`
  }
}

function moveOn () {
  // Next question or show summary
  hintContainer.style.display = 'none'
  questionNo++
  if (questionNo >= totalQuestions) {
    quizEndTime = new Date().toISOString().slice(0, 19).replace('T', ' ')
    recordQuizCompletion(quizStartTime, quizEndTime)
    state = SHOW_SUMMARY
    actionButton.innerHTML = 'Show Summary'
    actionButton.disabled = false
  } else {
    state = NEXT_QUESTION
    actionButton.innerHTML = 'Next Question'
    incorrectAttempts = 0
    hintUsed = false
  }
}

function processClick () {
  actionButton.disabled = true
  if (state === CHECK_ANSWER) {
    checkAnswer(quizOptions[questionNo])
    actionButton.disabled = false
    actionButton.focus()
  } else if (state === NEXT_QUESTION) {
    state = CHECK_ANSWER
    actionButton.innerHTML = 'Check Answer'
    feedbackContainer.innerHTML = ''
    askQuestion(quizOptions[questionNo])
  } else if (state === SHOW_SUMMARY) {
    const output = []
    actionButton.innerHTML = 'Back To Previous Page'
    state = DONE
    actionButton.disabled = false
    feedbackContainer.innerHTML = ''
    output.push(`
      <h1>Keep going. Keep growing.</h1>
      <p>${correctUnaidedTotal}/${totalQuestions} correct!</p>`)
    output.push(
        `<div class="table-responsive skills-table">
          <table class="table table-striped table-hover">
            <tbody>`
    )
    skillsMatrix.forEach(function (skill) {
      let change
      if (skill.new_mastery_level_id > skill.mastery_level_id) {
        change = '<i class="bi bi-arrow-up"></i>'
      } else if (skill.new_mastery_level_id === skill.mastery_level_id) {
        change = '<i class="bi bi-arrow-right"</i>'
      } else {
        change = '<i class="bi bi-arrow-down"></i>'
      }
      output.push(`
                <tr>
                  <td>${skill.title}</td>
                  <td class="text-right">${skill.mastery_level_desc}</td>
                  <td class="text-right">${change}</td>
                  <td class="text-right">${skill.new_mastery_level_desc}</td>
                </tr>`)
    })
    output.push(
            `</tbody>
          </table>
        </div><!-- table-responsive -->`)
    quizContainer.innerHTML = output.join('')
    if (correctUnaidedTotal === totalQuestions) {
      fireworks(5)
      hundredPercentSound.play()
    }
  } else if (state === DONE) {
    window.location.href = returnSlug
  }
}

// Choice is for multiple choice questions
// answer is for kas questions
function recordResponse (skillQuestionId, skillQuestionTypeId, choice, answer, correctOrNot, questionStartTime, questionEndTime) {
  let data
  if (skillQuestionTypeId === 1) {
    data = 'quizId=' + encodeURIComponent(quizId) + '&skillQuestionId=' + encodeURIComponent(skillQuestionId) + '&skillQuestionTypeId=' + encodeURIComponent(skillQuestionTypeId) + '&skillQuestionOptionId=' + encodeURIComponent(choice) + '&correctUnaided=' + encodeURIComponent(correctOrNot) + '&questionStartTime=' + encodeURIComponent(questionStartTime) + '&questionEndTime=' + encodeURIComponent(questionEndTime) + '&crsfToken=' + encodeURIComponent(crsfToken)
  } else if (skillQuestionTypeId === 2 || skillQuestionTypeId === 3) {
    data = 'quizId=' + encodeURIComponent(quizId) + '&skillQuestionId=' + encodeURIComponent(skillQuestionId) + '&skillQuestionTypeId=' + encodeURIComponent(skillQuestionTypeId) + '&answer=' + encodeURIComponent(answer) + '&correctUnaided=' + encodeURIComponent(correctOrNot) + '&questionStartTime=' + encodeURIComponent(questionStartTime) + '&questionEndTime=' + encodeURIComponent(questionEndTime) + '&crsfToken=' + encodeURIComponent(crsfToken)
  }
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  }
  fetch('/quizzes/questions/record', options)
    .then(response => response.json())
    .catch(error => { console.error(error) })
}

function recordQuizCompletion (quizStartTime, quizEndTime) {
  const data = 'quizId=' + encodeURIComponent(quizId) + '&quizStartTime=' + encodeURIComponent(quizStartTime) + '&quizEndTime=' + encodeURIComponent(quizEndTime) + '&crsfToken=' + encodeURIComponent(crsfToken)
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  }
  fetch('/quizzes/questions/record-completion', options)
    .then(response => response.json())
    .then(data => {
      if (data.status !== 'success') {
        throw new Error(data.message)
      }
      skillsMatrix = data.skillsMatrix
    })
    .catch(error => { console.error(error) })
}

/*
function convertToNumber (num) {
  return num.replace(/[^\d.-]/g, '')
}
*/
function showConfetti () {
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
}

function showWellDoneFeedback () {
  feedbackContainer.innerHTML = `
    <div class="alert alert-success alert-dismissible mt-2" role="alert">
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      <h4 class="alert-heading"><i class="bi bi-check"></i> Well Done!</h4>
      <p>Keep going!</p>
    </div>`
}

function correctWithoutHelp (incorrectAttempts, hintUsed) {
  if (incorrectAttempts === 0 && !hintUsed) {
    document.getElementById('dot' + questionNo).classList.remove('dot')
    document.getElementById('dot' + questionNo).classList.add('dotCorrect')
    correctUnaidedTotal++
    return 1
  }
  return 0
}

function retrieveMathfieldAnswerAndExpression (answerNum) {
  const mathfield = document.getElementById('mf_answer_' + answerNum)
  const studentAnswer = removeNonStandardLatex(mathfield.value)
  const studentExpr = KAS.parse(studentAnswer).expr
  return [mathfield, studentAnswer, studentExpr]
}

function isValidExpression (mathfield) {
  if (mathfield.value === '') {
    return false
  }
  const studentAnswer = removeNonStandardLatex(mathfield.value)
  const studentExpr = KAS.parse(studentAnswer)
  return studentExpr.parsed
}

function removeNonStandardLatex (input) {
  input = input.replace(/\\frac(\d)(\d)/g, '\\frac{$1}{$2}')
  input = input.replace(/\^{}/g, '')
  input = input.replace(/_{}/g, '')
  return input
}

function quizQuestionHtml (currentQuestion) {
  if (currentQuestion.question_image != null) {
    currentQuestion.question = currentQuestion.question.replace(/\{IMAGE\}/g, `<img class="questionImage" src="/uploads/skill-questions/${currentQuestion.question_image}" alt="Question" />`)
  }
  let mathfieldId = 0
  let numericInputId = 0
  currentQuestion.question = currentQuestion.question.replace(/\{MATHFIELD_SMALL\}/g, function () { return '<math-field id="mf_answer_" class="small"></math-field>' })
  currentQuestion.question = currentQuestion.question.replace(/\{MATHFIELD_LARGE\}/g, function () { return '<math-field id="mf_answer_" class="large"></math-field>' })
  currentQuestion.question = currentQuestion.question.replace(/\{MATHFIELD\}/g, function () { return '<math-field id="mf_answer_" class="standard"></math-field>' })
  currentQuestion.question = currentQuestion.question.replace(/<math-field id="mf_answer_"/g, function () { return '<math-field id="mf_answer_' + (mathfieldId++) + '"' })
  currentQuestion.question = currentQuestion.question.replace(/\{NUMERIC_INPUT_SMALL\}/g, function () { return '<input id="numeric_input_" type="text" class="numericInput small">' })
  currentQuestion.question = currentQuestion.question.replace(/\{NUMERIC_INPUT_LARGE\}/g, function () { return '<input id="numeric_input_" type="text" class="numericInput large">' })
  currentQuestion.question = currentQuestion.question.replace(/\{NUMERIC_INPUT\}/g, function () { return '<input id="numeric_input_" type="text" class="numericInput standard">' })
  currentQuestion.question = currentQuestion.question.replace(/<input id="numeric_input_"/g, function () { return '<input id="numeric_input_' + (numericInputId++) + '"' })
  return `<div class="quizQuestion">
         <p class="text-end text-decoration-underline">
           Question ${questionNo + 1} of ${totalQuestions}
         </p>
         ${currentQuestion.question}`
}

function getAnswerType (input) {
  input = input.replace(/\u2212/, '-').replace(/([+-])\s+/g, '$1')
  if (input.match(/^[+-]?\d+$/)) {
    return 'integer'
  }
  if (input.match(/^[+-]?\d+\s+\d+\s*\/\s*\d+$/)) {
    return 'mixed'
  }
  const fraction = input.match(/^([+-]?\d+)\s*\/\s*([+-]?\d+)$/)
  if (fraction) {
    return parseFloat(fraction[1]) > parseFloat(fraction[2])
      ? 'improper'
      : 'proper'
  }
  if (input.replace(/[,. ]/g, '').match(/^[+-]?\d+$/)) {
    return 'decimal'
  }
  if (input.match(/(pi?|\u03c0|t(?:au)?|\u03c4|pau)/)) {
    return 'pi'
  }
  return null
}

function fractionToDecimal (answer) {
  answer = answer
    // Replace unicode minus sign with hyphen
    .replace(/\u2212/, '-')
    // Remove space after +, -
    .replace(/([+-])\s+/g, '$1')
    // Remove leading/trailing whitespace
    .replace(/(^\s*)|(\s*$)/gi, '')
  const fraction = answer.match(/^([+-]?\d+)\s*\/\s*([+-]?\d+)$/)
  if (fraction) {
    const numerator = parseFloat(fraction[1])
    const denominator = parseFloat(fraction[2])
    const value = numerator / denominator
    const simplified =
      denominator !== 0 &&
      getGCD(numerator, denominator) === 1
    return {
      numerator,
      denominator,
      value,
      simplified
    }
  }
}

function getGCD (a, b) {
  let mod

  a = Math.abs(a)
  b = Math.abs(b)

  while (b) {
    mod = a % b
    a = b
    b = mod
  }

  return a
}

// display quiz after start button pressed
startButton.addEventListener('click', fetchQuestions)

actionButton.disabled = true
actionButton.addEventListener('click', processClick)
