// JSON-RPC INIT
$(document).ready(function() {
	api = jQuery.Zend.jsonrpc({
		url : '/api/v1.0/json.php'
	});
});

// /JSON-RPC INIT

// Load templates
$(document).ready(function() {
	$.holdReady(true);
	loadTemplates();
	$.holdReady(false);
});
// /Load templates

// Menus
$(document).ready(function() {
	$(".topNav").wijmenu();
	$("#categoryNavigation").wijmenu({
		orientation : 'vertical'
	});
	$(".modularNavigation").wijmenu({
		orientation : 'vertical'
	});
});
// /Menus

// Course Search interaction
$(document).ready(function() {

	$("body").delegate("div.srchbar input.inpt", "keyup", function(e) {
		if (e.keyCode == 13) {
			searchCourses();
		}
	});
	$("body").delegate("div.srchbar input.go", "click", function(e) {
		searchCourses();
	});
});
// /Course Search interaction

// Template loading
function loadTemplates() {

	if (window && window.HandlebarsTemplates !== undefined) {
		// Templates are already loaded.
		return true;
	} else {
		window.HandlebarsTemplates = 'loading...';
		var HandlebarsTemplates = {};
		$("link[type='application/x-handlebars-template']").each(function() {
			var urlText = $(this).attr("href");
			var templateName = $(this).data('template');
			$.ajax({
				url : urlText,
				async : false,
				datatype : 'text',
				success : function(response, status, jqXHR) {
					var template = Handlebars.compile(jqXHR.responseText);
					HandlebarsTemplates[templateName] = template;
				}
			});
		});
		window.HandlebarsTemplates = HandlebarsTemplates;
		console.log("templates loaded into global window.HandlebarsTemplates.");
		return true;
	}

}
// /Template loading

/**
 * Execute all of the course viewing page functionality here.
 */
function coursePageLoad() {
	// Load the templates if not already loaded.
	loadTemplates();
	// Sliders
	$('.slide_div').hide();
	$('.slide_div:first').show();
	$("div#course_progress .slider span").removeClass().addClass(
			"toggleminimized");
	$("div#announcements .slider span").removeClass().addClass(
			"togglemaximized");
	$("div#questions .slider span").removeClass().addClass("togglemaximized");

	$("div.slider").click(
			function() {
				var flag = $(this).parent().find('.slide_div').css('display');

				$('.slide_div').slideUp(1000);
				$("div.slider span").removeClass('class').addClass(
						'togglemaximized');
				if (flag == 'none') {
					$(this).find('span').removeClass('togglemaximized')
							.addClass('toggleminimized');
					$(this).parent().find('.slide_div').slideDown(1000);
				} else {
					$(this).find('span').removeClass('class').addClass(
							'togglemaximized');
					$(this).parent().find('.slide_div').slideUp(1000);
				}
			});
	// /SLIDERS

	// populate announcements widget
	course_announcements_list(window.courseId);
	// populate Q&A widget
	course_question_list(window.courseId);

	// Q&A
	// User clicks on a question link -- Show them answers to the
	// question
	$("body").delegate("div#questionsWidget a.questionLink", "click",
			function(event) {
				course_question_listAnswers($(this).data('id'));
			});

	// User wants to delete a course question
	$("body").delegate("div#questionsWidget a.deleteQuestion", "click",
			function(event) {
				course_question_remove($(this).data('id'));
			});

	// User wants to delete a course question
	$("body").delegate("div#questionsWidget a.deleteAnswer", "click",
			function(event) {
				course_question_answer_remove($(this).data('id'));
			});

	// User clicks back button - Display list of questions
	$("body").delegate("div#questionsWidget a.backToQuestionsLink", "click",
			function(event) {
				course_question_list($(this).data('id'));
			});

	// User asks a new question by hitting the enter/return key in the
	// input
	$("body")
			.delegate(
					"div#questionsWidget :input.askAQuestion",
					"keyup",
					function(e) {
						if (e.keyCode == 13) {
							var input = $(this).val();
							$(this).val("");
							if (input) {
								if (api.question.acl_askCourseQuestion(
										courseId, input)) {
									course_question_list(window.courseId);
								}

							} else {
								console.log("No input provided.");
							}

						}
					});

	// User answers an existing question by hittin the enter/return key
	// in the input
	$("body").delegate(
			"div#questionsWidget :input.answerAQuestion",
			"keyup",
			function(e) {
				if (e.keyCode == 13) {
					var input = $(this).val();
					var questionId = $(this).data('id');
					$(this).val("");
					if (input) {
						if (api.question.acl_answerCourseQuestion(questionId,
								input)) {
							course_question_listAnswers(questionId);
						}

					} else {
						console.log("No input provided.");
					}

				}
			});
	// /Q&A

	// ANNOUNCEMENTS
	$("body")
			.delegate(
					"div#announcementsWidget a",
					"click",
					function(event) {

						// Determine actions based on the
						// data-action parameter

						$action = $(this).data("action");
						switch ($action) {
						case "view":
							var announcement = api.course
									.acl_findAnnouncement($(this).data("id"));
							$('div#announcementsDialog')
									.html(announcement.text);
							$('div#announcementsDialog').wijdialog({
								autoOpen : true,
								captionButtons : {
									refresh : {
										visible : false
									}
								}
							});
							break;
						case "create":
							$("div#announcementsWidget").slideUp("slow");
							$("div#announcementsWidget")
									.queue(
											function() {
												var template = window.HandlebarsTemplates.Site_Course_Announcement_Create;
												var input = {};
												$('div#announcementsWidget')
														.html(template(input));
												$("div#announcementsWidget")
														.slideDown("slow");
												$(this).dequeue();
											});
							break;

						case "back":
							course_announcements_list(window.courseId);
							break;

						case "post":

							var announcementText = $("#announcementInput")
									.val();
							var result = api.course
									.acl_providerCreateAnnouncement(courseId,
											announcementText, true);
							if (result) {
								course_announcements_list(window.courseId);
							} else {
								alert("Unable to post the announcement.");
							}
							break;

						default:

						}
					});
	// /ANNOUNCEMENTS
}

function searchCourses() {
	var template = window.HandlebarsTemplates.Site_Course_List;
	var q = $("div.srchbar input.inpt").val();
	var result = api.course.search(q);
	var input = {
		courses : result
	};

	for (i in result) {
		var courseId = result[i].id;
		result[i].image_url = api.course.acl_getImageUrl(courseId);
	}
	$("div.course_div").html(template(input));
}

function course_question_list(courseId) {
	var result = api.question.acl_listCourseQuestions(courseId);
	var input = {
		"questions" : result,
		"courseId" : courseId
	};

	if (window.HandlebarsTemplates.Site_Course_Questions) {

	} else {

	}

	var template = window.HandlebarsTemplates.Site_Course_Questions;
	$('div#questionsWidget').html(template(input));

}

function course_question_ask(text) {

}

function course_question_answer(text) {

}

function course_question_remove(questionId) {
	var result = api.question.acl_removeCourseQuestion(questionId);
	if (result) {
		course_question_list(window.courseId);
	} else {
		alert("unable to delete that question");
	}
}

function course_question_answer_remove(answerId) {
	var result = api.question.acl_removeCourseQuestionAnswer(answerId);
	if (result) {
		course_question_list(window.courseId);
	} else {
		alert("unable to delete that answer");
	}
}

function course_question_listAnswers(questionId) {
	var result = api.question.acl_listCourseQuestionAnswers(questionId);
	var questionArr = api.question.acl_findCourseQuestion(questionId);

	for (e in result) {
		result[e].canDelete = true;
	}

	var input = {
		"answers" : result,
		"courseId" : window.courseId,
		"questionText" : questionArr.text,
		"canDelete" : true,
		"questionId" : questionId,
		"_username" : questionArr._username
	};

	$("div#questionsWidget").slideUp("slow");
	$("div#questionsWidget").queue(function() {
		var template = window.HandlebarsTemplates.Site_Course_Question_Answers;
		$('div#questionsWidget').html(template(input));
		$("div#questionsWidget").slideDown("slow");
		$(this).dequeue();
	});
}

function content_question_list(contentId) {

}
function course_announcements_list(courseId) {
	var result = api.course.acl_listAnnouncements(courseId);
	for (e in result) {
		if (result[e].text) {
			var title = result[e].text.substr(0, 50) + "...";
			result[e].title = title;
		}
	}
	var input = {
		canPost : true,
		announcements : result
	};
	$("div#announcementsWidget").slideUp("slow");
	$("div#announcementsWidget").queue(function() {
		var template = window.HandlebarsTemplates.Site_Course_Announcements;
		$('div#announcementsWidget').html(template(input));
		$("div#announcementsWidget").slideDown("slow");
		$(this).dequeue();
	});
}

function course_subscription_information(courseId) {

}

function course_list_content_order(courseId) {
	var result = api.course.acl_listContentOrder(courseId);
	var template = window.HandlebarsTemplates.Site_Course_Content_Sortable;
	var input = {
		'sections' : result
	};

	$('body').html(template(input));
	$("div.course_content_sortable ol").nestedSortable({
		items : 'li',
		placeholder : "ui-state-highlight",
		maxLevels : 1,
		protectRoot : true,
		disableNesting : "no-nesting"
	});

	$("div.course_content_sortable ol").on("sortstop", function(event, ui) {
		$("div.course_content_sortable div.chapter").each(function() {
			console.log($(this).data("id"));
		});
	});
}

function markComplete(courseId, contentId) {
	var result = api.course.acl_completeContent(courseId, contentId);
	if (result == true) {
		$("a.isCompleted").text("Mark Incomplete").attr("onclick",
				'markIncomplete(' + courseId + ',' + contentId + ')');
	}

}
function markIncomplete(courseId, contentId) {
	var result = api.course.acl_uncompleteContent(courseId, contentId);
	if (result == true) {
		$("a.isCompleted").text("Mark Complete").attr("onclick",
				'markComplete(' + courseId + ',' + contentId + ')');
	}
}
