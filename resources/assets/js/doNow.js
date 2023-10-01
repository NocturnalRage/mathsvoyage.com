const questionCanvas = document.getElementById('questionCanvas')
const questionCtx = questionCanvas.getContext('2d')
const answerCanvas = document.getElementById('answerCanvas')
const answerCtx = answerCanvas.getContext('2d')
questionCtx.lineWidth = 5
answerCtx.lineWidth = 5

document.getElementById('downloadBtn').addEventListener('click', downloadDoNow)

const question1 = document.getElementById('question-1')
if (question1.complete) {
  questionCtx.drawImage(question1, 0, 0)
  questionCtx.beginPath()
  questionCtx.rect(0, 0, 640, 360)
  questionCtx.stroke()
} else {
  question1.addEventListener('load', (e) => {
    questionCtx.drawImage(question1, 0, 0)
    questionCtx.beginPath()
    questionCtx.rect(0, 0, 640, 360)
    questionCtx.stroke()
  })
}
const answer1 = document.getElementById('answer-1')
if (answer1.complete) {
  answerCtx.drawImage(answer1, 0, 0)
  answerCtx.beginPath()
  answerCtx.rect(0, 0, 640, 360)
  answerCtx.stroke()
} else {
  answer1.addEventListener('load', (e) => {
    answerCtx.drawImage(answer1, 0, 0)
    answerCtx.beginPath()
    answerCtx.rect(0, 0, 640, 360)
    answerCtx.stroke()
  })
}

const question2 = document.getElementById('question-2')
if (question2.complete) {
  questionCtx.drawImage(question2, 640, 0)
  questionCtx.beginPath()
  questionCtx.rect(640, 0, 640, 360)
  questionCtx.stroke()
} else {
  question2.addEventListener('load', (e) => {
    questionCtx.drawImage(question2, 640, 0)
    questionCtx.beginPath()
    questionCtx.rect(640, 0, 640, 360)
    questionCtx.stroke()
  })
}
const answer2 = document.getElementById('answer-2')
if (answer2.complete) {
  answerCtx.drawImage(answer2, 640, 0)
  answerCtx.beginPath()
  answerCtx.rect(640, 0, 640, 360)
  answerCtx.stroke()
} else {
  answer2.addEventListener('load', (e) => {
    answerCtx.drawImage(answer2, 640, 0)
    answerCtx.beginPath()
    answerCtx.rect(640, 0, 640, 360)
    answerCtx.stroke()
  })
}

const question3 = document.getElementById('question-3')
if (question3.complete) {
  questionCtx.drawImage(question3, 1280, 0)
  questionCtx.beginPath()
  questionCtx.rect(1280, 0, 640, 360)
  questionCtx.stroke()
} else {
  question3.addEventListener('load', (e) => {
    questionCtx.drawImage(question3, 1280, 0)
    questionCtx.beginPath()
    questionCtx.rect(1280, 0, 640, 360)
    questionCtx.stroke()
  })
}
const answer3 = document.getElementById('answer-3')
if (answer3.complete) {
  answerCtx.drawImage(answer3, 1280, 0)
  answerCtx.beginPath()
  answerCtx.rect(1280, 0, 640, 360)
  answerCtx.stroke()
} else {
  answer3.addEventListener('load', (e) => {
    answerCtx.drawImage(answer3, 1280, 0)
    answerCtx.beginPath()
    answerCtx.rect(1280, 0, 640, 360)
    answerCtx.stroke()
  })
}

const question4 = document.getElementById('question-4')
if (question4.complete) {
  questionCtx.drawImage(question4, 0, 360)
  questionCtx.beginPath()
  questionCtx.rect(0, 360, 640, 360)
  questionCtx.stroke()
} else {
  question4.addEventListener('load', (e) => {
    questionCtx.drawImage(question4, 0, 360)
    questionCtx.beginPath()
    questionCtx.rect(0, 360, 640, 360)
    questionCtx.stroke()
  })
}
const answer4 = document.getElementById('answer-4')
if (answer4.complete) {
  answerCtx.drawImage(answer4, 0, 360)
  answerCtx.beginPath()
  answerCtx.rect(0, 360, 640, 360)
  answerCtx.stroke()
} else {
  answer4.addEventListener('load', (e) => {
    answerCtx.drawImage(answer4, 0, 360)
    answerCtx.beginPath()
    answerCtx.rect(0, 360, 640, 360)
    answerCtx.stroke()
  })
}

const question5 = document.getElementById('question-5')
if (question5.complete) {
  questionCtx.drawImage(question5, 640, 360)
  questionCtx.beginPath()
  questionCtx.rect(640, 360, 640, 360)
  questionCtx.stroke()
} else {
  question5.addEventListener('load', (e) => {
    questionCtx.drawImage(question5, 640, 360)
    questionCtx.beginPath()
    questionCtx.rect(640, 360, 640, 360)
    questionCtx.stroke()
  })
}
const answer5 = document.getElementById('answer-5')
if (answer5.complete) {
  answerCtx.drawImage(answer5, 640, 360)
  answerCtx.beginPath()
  answerCtx.rect(640, 360, 640, 360)
  answerCtx.stroke()
} else {
  answer5.addEventListener('load', (e) => {
    answerCtx.drawImage(answer5, 640, 360)
    answerCtx.beginPath()
    answerCtx.rect(640, 360, 640, 360)
    answerCtx.stroke()
  })
}

const question6 = document.getElementById('question-6')
if (question6.complete) {
  questionCtx.drawImage(question6, 1280, 360)
  questionCtx.beginPath()
  questionCtx.rect(1280, 360, 640, 360)
  questionCtx.stroke()
} else {
  question6.addEventListener('load', (e) => {
    questionCtx.drawImage(question6, 1280, 360)
    questionCtx.beginPath()
    questionCtx.rect(1280, 360, 640, 360)
    questionCtx.stroke()
  })
}
const answer6 = document.getElementById('answer-6')
if (answer6.complete) {
  answerCtx.drawImage(answer6, 1280, 360)
  answerCtx.beginPath()
  answerCtx.rect(1280, 360, 640, 360)
  answerCtx.stroke()
} else {
  answer6.addEventListener('load', (e) => {
    answerCtx.drawImage(answer6, 1280, 360)
    answerCtx.beginPath()
    answerCtx.rect(1280, 360, 640, 360)
    answerCtx.stroke()
  })
}

const question7 = document.getElementById('question-7')
if (question7.complete) {
  questionCtx.drawImage(question7, 0, 720)
  questionCtx.beginPath()
  questionCtx.rect(0, 720, 640, 360)
  questionCtx.stroke()
} else {
  question7.addEventListener('load', (e) => {
    questionCtx.drawImage(question7, 0, 720)
    questionCtx.beginPath()
    questionCtx.rect(0, 720, 640, 360)
    questionCtx.stroke()
  })
}
const answer7 = document.getElementById('answer-7')
if (answer7.complete) {
  answerCtx.drawImage(answer7, 0, 720)
  answerCtx.beginPath()
  answerCtx.rect(0, 720, 640, 360)
  answerCtx.stroke()
} else {
  answer7.addEventListener('load', (e) => {
    answerCtx.drawImage(answer7, 0, 720)
    answerCtx.beginPath()
    answerCtx.rect(0, 720, 640, 360)
    answerCtx.stroke()
  })
}

const question8 = document.getElementById('question-8')
if (question8.complete) {
  questionCtx.drawImage(question8, 640, 720)
  questionCtx.beginPath()
  questionCtx.rect(640, 720, 640, 360)
  questionCtx.stroke()
} else {
  question8.addEventListener('load', (e) => {
    questionCtx.drawImage(question8, 640, 720)
    questionCtx.beginPath()
    questionCtx.rect(640, 720, 640, 360)
    questionCtx.stroke()
  })
}
const answer8 = document.getElementById('answer-8')
if (answer8.complete) {
  answerCtx.drawImage(answer8, 640, 720)
  answerCtx.beginPath()
  answerCtx.rect(640, 720, 640, 360)
  answerCtx.stroke()
} else {
  answer8.addEventListener('load', (e) => {
    answerCtx.drawImage(answer8, 640, 720)
    answerCtx.beginPath()
    answerCtx.rect(640, 720, 640, 360)
    answerCtx.stroke()
  })
}

const question9 = document.getElementById('question-9')
if (question9.complete) {
  questionCtx.drawImage(question9, 1280, 720)
  questionCtx.beginPath()
  questionCtx.rect(1280, 720, 640, 360)
  questionCtx.stroke()
} else {
  question9.addEventListener('load', (e) => {
    questionCtx.drawImage(question9, 1280, 720)
    questionCtx.beginPath()
    questionCtx.rect(1280, 720, 640, 360)
    questionCtx.stroke()
  })
}
const answer9 = document.getElementById('answer-9')
if (answer9.complete) {
  answerCtx.drawImage(answer9, 1280, 720)
  answerCtx.beginPath()
  answerCtx.rect(1280, 720, 640, 360)
  answerCtx.stroke()
} else {
  answer9.addEventListener('load', (e) => {
    answerCtx.drawImage(answer9, 1280, 720)
    answerCtx.beginPath()
    answerCtx.rect(1280, 720, 640, 360)
    answerCtx.stroke()
  })
}

function downloadDoNow (num) {
  questionCanvas.focus()
  const questiondownloadLink = document.createElement('a')
  questiondownloadLink.setAttribute('download', 'questions.png')
  questionCanvas.toBlob(function (blob) {
    const url = URL.createObjectURL(blob)
    questiondownloadLink.setAttribute('href', url)
    questiondownloadLink.click()
  })

  answerCanvas.focus()
  const answerdownloadLink = document.createElement('a')
  answerdownloadLink.setAttribute('download', 'answers.png')
  answerCanvas.toBlob(function (blob) {
    const url = URL.createObjectURL(blob)
    answerdownloadLink.setAttribute('href', url)
    answerdownloadLink.click()
  })
}
