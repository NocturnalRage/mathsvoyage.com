<?php

return [
    ['GET', '/page-not-found', 'PageNotFoundController@index', 'PageNotFoundIndex'],
    ['GET', '/', 'StaticPageController@home', 'StaticPageHome'],
    ['GET', '/arithmetic', 'StaticPageController@arithmetic', 'StaticPageArithmetic'],
    ['GET', '/login', 'AuthenticationController@loginForm', 'AuthenticationLoginForm'],
    ['POST', '/login', 'AuthenticationController@loginValidate', 'AuthenticationLoginValidate'],
    ['POST', '/logout', 'AuthenticationController@logout', 'AuthenticationLogout'],
    ['GET', '/register', 'AuthenticationController@registerForm', 'AuthenticationRegisterForm'],
    ['POST', '/register', 'AuthenticationController@registerValidate', 'AuthenticationRegisterValidate'],
    ['GET', '/confirm-registration', 'AuthenticationController@registerConfirm', 'AuthenticationRegisterConfirm'],
    ['GET', '/activate-free-membership', 'AuthenticationController@registerActivate', 'AuthenticationRegisterActivate'],
    ['GET', '/thanks-for-registering', 'AuthenticationController@registerThanks', 'AuthenticationRegisterThanks'],
    ['GET', '/password', 'AuthenticationController@password', 'AuthenticationPassword'],
    ['POST', '/password-validate', 'AuthenticationController@password_validate', 'AuthenticationPasswordValidate'],
    ['GET', '/forgot-password', 'AuthenticationController@forgot_password', 'AuthenticationForgotPassword'],
    ['POST', '/forgot-password-validate', 'AuthenticationController@forgot_password_validate', 'AuthenticationForgotPasswordValidate'],
    ['GET', '/reset-password', 'AuthenticationController@reset_password', 'AuthenticationResetPassword'],
    ['POST', '/reset-password-validate', 'AuthenticationController@reset_password_validate', 'AuthenticationResetPasswordValidate'],
    ['GET', '/forgot-password-confirm', 'AuthenticationController@forgot_password_confirm', 'AuthenticationForgotPasswordConfirm'],
    ['GET', '/profile', 'AuthenticationController@profile', 'AuthenticationProfile'],
    ['GET', '/profile/edit', 'AuthenticationController@profileEdit', 'AuthenticationProfileEdit'],
    ['PATCH', '/profile/edit', 'AuthenticationController@profileUpdate', 'AuthenticationProfileUpdate'],
    ['GET', '/curriculum', 'CurriculaController@index', 'CurriculaIndex'],
    ['POST', '/curriculum', 'CurriculaController@create', 'CurriculaCreate'],
    ['GET', '/curriculum/new', 'CurriculaController@new', 'CurriculaNew'],
    ['GET', '/curriculum/[:slug]/edit', 'CurriculaController@edit', 'CurriculaEdit'],
    ['GET', '/curriculum/[:slug]', 'CurriculaController@show', 'CurriculaShow'],
    ['GET', '/curriculum/[:curriculumSlug]/quiz', 'CurriculaController@quiz', 'CurriculaQuiz'],
    ['POST', '/curriculum/[:curriculumSlug]/quiz/create', 'CurriculaController@create_quiz', 'CurriculaCreateQuiz'],
    ['PATCH', '/curriculum/[:slug]', 'CurriculaController@update', 'CurriculaUpdate'],
    ['GET', '/topics', 'TopicsController@index', 'TopicsIndex'],
    ['POST', '/topics', 'TopicsController@create', 'TopicsCreate'],
    ['GET', '/topics/new', 'TopicsController@new', 'TopicsNew'],
    ['GET', '/topics/[:curriculumSlug]/[:topicSlug]', 'TopicsController@show', 'TopicsShow'],
    ['GET', '/topics/[:curriculumSlug]/[:topicSlug]/quiz', 'TopicsController@quiz', 'TopicsQuiz'],
    ['POST', '/topics/[:curriculumSlug]/[:topicSlug]/quiz/create', 'TopicsController@create_quiz', 'TopicsCreateQuiz'],
    ['GET', '/skills', 'SkillsController@index', 'SkillsIndex'],
    ['POST', '/skills', 'SkillsController@create', 'SkillsCreate'],
    ['GET', '/skills/new', 'SkillsController@new', 'SkillsNew'],
    ['GET', '/skills/[:curriculumSlug]/[:topicSlug]/[:skillSlug]', 'SkillsController@show', 'SkillsShow'],
    ['GET', '/skills/[:curriculumSlug]/[:topicSlug]/[:skillSlug]/quiz', 'SkillsController@quiz', 'SkillsQuiz'],
    ['GET', '/skills/[:curriculumSlug]/[:topicSlug]/[:skillSlug]/videos', 'SkillsController@videos', 'SkillsVideos'],
    ['GET', '/skills/[:curriculumSlug]/[:topicSlug]/[:skillSlug]/worked-solutions', 'SkillsController@worked_solutions', 'SkillsWorkedSolutions'],
    ['POST', '/skills/[:curriculumSlug]/[:topicSlug]/[:skillSlug]/quiz/create', 'SkillsController@create_quiz', 'SkillsCreateQuiz'],
    ['GET', '/skills/[:curriculumSlug]/[:topicSlug]/[:skillSlug]/worksheet/[:categoryId]/[:numQuestions]', 'SkillsController@worksheet', 'Skillsworksheet'],
    ['POST', '/skill-questions', 'SkillQuestionsController@create', 'SkillQuestionsCreate'],
    ['GET', '/skill-questions/new', 'SkillQuestionsController@new', 'SkillQuestionsNew'],
    ['POST', '/skill-questions/createKasAnswer', 'SkillQuestionsController@createKasAnswer', 'SkillQuestionsCreateKasAnswer'],
    ['POST', '/skill-questions/createNumericAnswer', 'SkillQuestionsController@createNumericAnswer', 'SkillQuestionsCreateNumericAnswer'],
    ['GET', '/skill-questions/newKasAnswer', 'SkillQuestionsController@newKasAnswer', 'SkillQuestionsNewKasAnswer'],
    ['GET', '/skill-questions/newNumericAnswer', 'SkillQuestionsController@newNumericAnswer', 'SkillQuestionsNewNumericAnswer'],
    ['GET', '/skill-questions/[:skillQuestionId]', 'SkillQuestionsController@show', 'SkillQuestionsShow'],
    ['POST', '/videos', 'VideosController@create', 'VideosCreate'],
    ['GET', '/videos/new', 'VideosController@new', 'VideosNew'],
    ['POST', '/worked-solutions', 'WorkedSolutionsController@create', 'WorkedSolutionsCreate'],
    ['GET', '/worked-solutions/new', 'WorkedSolutionsController@new', 'WorkedSolutionsNew'],
    ['GET', '/worked-solutions/fetch/[:skillId]', 'WorkedSolutionsController@fetch', 'WorkedSolutionsFetch'],
    ['GET', '/quizzes/questions/[:quizId]', 'QuizzesController@fetchQuiz', 'quizzesFetchQuiz'],
    ['POST', '/quizzes/questions/record', 'QuizzesController@recordResponse', 'quizzesRecordResponse'],
    ['POST', '/quizzes/questions/record-completion', 'QuizzesController@recordCompletion', 'quizzesRecordComplettion'],
    ['GET', '/times-tables', 'TimesTablesController@index', 'TimesTablesIndex'],
    ['GET', '/times-tables/quiz', 'TimesTablesController@quiz', 'TimesTablesQuiz'],
    ['POST', '/times-tables/quizzes/record-score', 'TimesTablesController@record_score', 'TimesTablesRecordScore'],
    ['POST', '/times-tables/quizzes/complete-attempt', 'TimesTablesController@complete_attempt', 'TimesTablesCompleteAttempt'],
    ['POST', '/times-tables/quizzes/increment-attempt', 'TimesTablesController@increment_attempt', 'TimesTablesIncrementAttempt'],
    ['POST', '/times-tables/quizzes/increment-times-table', 'TimesTablesController@increment_times_table', 'TimesTablesIncrementTimesTable'],
    ['GET', '/general-arithmetic', 'GeneralArithmeticController@index', 'GeneralArithmeticIndex'],
    ['GET', '/general-arithmetic/quiz', 'GeneralArithmeticController@quiz', 'GeneralArithmeticQuiz'],
    ['POST', '/general-arithmetic/quizzes/record-score', 'GeneralArithmeticController@record_score', 'GeneralArithmeticRecordScore'],
    ['GET', '/do-now/[:skillId]/[:doNowState]', 'DoNowController@show', 'DoNowShow'],
];
