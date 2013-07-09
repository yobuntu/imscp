	/*================================
	File Uploader
	=================================*/
	function createUploader() {
	  var uploader = new qq.FileUploader({
		  element: document.getElementById('file-uploader'),
		  action: 'do-nothing.htm',
		  debug: true
	  });
	}
	
	/* in your app create uploader as soon as the DOM is ready don't wait for the window to load  */
		
	window.onload = createUploader; 
	  
	/*================================
	Scroll Top
	=================================*/
	$(function () {
			$("#goTop").hide();
			$(window).scroll(function () {
				if ($(this).scrollTop() > 100) {
					$('#goTop').fadeIn();
				} else {
					$('#goTop').fadeOut();
				}
			});
	
			$('#goTop a').click(function () {
				$('body,html').animate({
					scrollTop: 0
				}, 500);
				return false;
			});
		});
	
		
	//You need an anonymous function to wrap around your function to avoid conflict
	
	(function($){
	 
		//Attach this new method to jQuery
		$.fn.extend({
			 
			//This is where you write your plugin's name
			slideMnu: function() {
		  
			  $('.right-toggle').click(function()
			{
				$('#panel-right').toggleClass('panel-close panel-open',500, 'easeOutExpo');
				});
			 
			}
		});
		 
	})(jQuery);
		
	
	/*================================
	jQuery Notify
	=================================*/
		  
	function create( template, vars, opts ){
		return $container.notify("create", template, vars, opts);
	}
	
	$(function(){
		// initialize widget on a container, passing in all the defaults.
		// the defaults will apply to any notification created within this
		// container, but can be overwritten on notification-by-notification
		// basis.
		$container = $("#container-n").notify();
		
		// create two when the pg loads
		create("default", { title:'Default Notification', text:'Example of a default notification.  I will fade out after 5 seconds'});
		create("sticky", { title:'Sticky Notification', text:'Example of a "sticky" notification.  Click on the X above to close me.'},{ expires:false });
		
		// bindings for the examples
		$(".default").click(function(){
			create("default", { title:'Default Notification', text:'Example of a default notification.  I will fade out after 5 seconds'});
		});
		
		$(".sticky").click(function(){
			create("sticky", { title:'Sticky Notification', text:'Example of a "sticky" notification.  Click on the X above to close me.'},{ expires:false });
		});
		
		$(".warning").click(function(){
			create("withIcon", { title:'Warning!', text:'OMG the quick brown fox jumped over the lazy dog.  You\'ve been warned. <a href="#" class="ui-notify-close notify-close-button">Close me.</a>', icon:'alert.png' },{ 
				expires:false
			});
		});
		
		
		
		$(".clickable").click(function(){
			create("default", { title:'Clickable Notification', text:'Click on me to fire a callback. Do it quick though because I will fade out after 5 seconds.'}, {
				click: function(e,instance){
					alert("Click triggered!\n\nTwo options are passed into the click callback: the original event obj and the instance object.");
				}
			});
		});
		
		$(".buttons").click(function(){
			var n = create("buttons", { title:'Confirm some action', text:'This template has a button.' },{ 
				expires:false
			});
			
			n.widget().delegate("input","click", function(){
				n.close();
			});
		});
		
		// second
		var container = $("#container-bottom").notify({ stack:'above' });
		container.notify("create", { 
			title:'Look ma, two containers!', 
			text:'This container is positioned on the bottom of the screen.  Notifications will stack on top of each other with the <code>position</code> attribute set to <code>above</code>.' 
		},{ expires:false });
		
		container.notify("widget").find("input").bind("click", function(){
			container.notify("create", 1, { title:'Another Notification!', text:'The quick brown fox jumped over the lazy dog.' });
		});
		
		// third
		var container = $("#container-bottom-right").notify();
		$(".queue").click(function(){
			container.notify("create", "queueing", {
				title:'Queueing', text:'Example of a notification with queuing.  For this container, no more than three notifications of this type will be visible at one time.'
			}, { queue: 3 });
		});
	});


$(document).ready(function(){

	/*===================
	COLOR PICKER
	===================*/
	  		
		$('#colorSelector').ColorPicker({
		color: '#0000ff',
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#colorSelector div').css('backgroundColor', '#' + hex);
		}
	});
		
	/*===================
	TAB STYLE
	===================*/	  
			  
	$(".tab-block").hide(); //Hide all content
	$(".mytabs li:first").addClass("active").show(); //Activate first tab
	$(".tab-block:first").show(); //Show first tab content
	
	//On Click Event
	$(".mytabs li").click(function() {
	
	$(".mytabs li").removeClass("active"); //Remove any "active" class
	$(this).addClass("active"); //Add "active" class to selected tab
	$(".tab-block").hide(); //Hide all tab content
	
	var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
	$(activeTab).show(); //Fade in the active ID content
	return false;
	});
	
	/*===================
	LIST-ACCORDION
	===================*/	  

	$('#list-accordion').accordion({
		header: ".title",
		autoheight: false
	});
	
	/*===================
	ACCORDION NAV
	===================*/
		
	$('#accordion-nav').accordion({
	active: false,
	header: '.head',
	navigation: true,
	event: 'click',
	fillSpace: false
	});
	
	/*===================
	ADMINISTRATION MENU
	===================*/

	$('#usermenu').click(function () {
	$('.admin-user').addClass('active');
	$('.sub-menu').slideToggle('fast');
	});
	
	$(document).click(function(e) {
        var t = (e.target)
            if(t!= $(".sub-menu").get(0) && t!=$(".admin-user").get(0) ) {
                 $('.admin-user').removeClass('active');
				$(".sub-menu").hide();
            }
    });
	

	/*====================
	ANIMATED PROGRESS BAR
	======================*/
    jQuery.fn.anim_progressbar = function (aOptions) {
        // def values
        var iCms = 1000;
        var iMms = 60 * iCms;
        var iHms = 3600 * iCms;
        var iDms = 24 * 3600 * iCms;

        /*--Var Options--*/
        var aDefOpts = {
            start: new Date(), // now
            finish: new Date().setTime(new Date().getTime() + 60 * iCms), // now + 60 sec
            interval: 100
        }
        var aOpts = jQuery.extend(aDefOpts, aOptions);
        var vPb = this;

       /*--each progress bar--*/
        return this.each(
            function() {
                var iDuration = aOpts.finish - aOpts.start;

                 /*--calling original progressbar--*/
                $(vPb).children('.pbar').progressbar();

                 /*--looping process--*/ 
                var vInterval = setInterval(
                    function(){
                        var iLeftMs = aOpts.finish - new Date(); 
                        var iElapsedMs = new Date() - aOpts.start, 
                            iDays = parseInt(iLeftMs / iDms), // elapsed days
                            iHours = parseInt((iLeftMs - (iDays * iDms)) / iHms), // elapsed hours
                            iMin = parseInt((iLeftMs - (iDays * iDms) - (iHours * iHms)) / iMms), // elapsed minutes
                            iSec = parseInt((iLeftMs - (iDays * iDms) - (iMin * iMms) - (iHours * iHms)) / iCms), // elapsed seconds
                            iPerc = (iElapsedMs > 0) ? iElapsedMs / iDuration * 100 : 0; // percentages

                        /*--display current positions and progress--*/ 
                        $(vPb).children('.percent').html('<b>'+iPerc.toFixed(1)+'%</b>');
                        $(vPb).children('.elapsed').html(iDays+' days '+iHours+'h:'+iMin+'m:'+iSec+'s</b>');
                        $(vPb).children('.pbar').children('.ui-progressbar-value').css('width', iPerc+'%');

                        /*--in case of Finish--*/ 
                        if (iPerc >= 100) {
                            clearInterval(vInterval);
                            $(vPb).children('.percent').html('<b>100%</b>');
                            $(vPb).children('.elapsed').html('Finished');
                        }
                    } ,aOpts.interval
                );
            }
        );
    }

    /*--Deafult--*/  
    $('#progress1').anim_progressbar();

    /*--from second #5 till 15--*/  
    var iNow = new Date().setTime(new Date().getTime() + 5 * 1000); // now plus 5 secs
    var iEnd = new Date().setTime(new Date().getTime() + 15 * 1000); // now plus 15 secs
    $('#progress2').anim_progressbar({start: iNow, finish: iEnd, interval: 100});

    /*--we will just set interval of updating to 1 sec--*/   
    $('#progress3').anim_progressbar({interval: 1000});
	
	
	/*===================
	DATA TABLE
	===================*/			
	$('.data-table').dataTable();
	  $('.data-grid').dataTable({
		  "sPaginationType": "full_numbers",
		   "bSort": false,
		  });
	$('.data-table-theme').dataTable({
	"sPaginationType": "full_numbers",
	});
	
	$('.data-table-noConfig').dataTable( {
	"bPaginate": false,
	"bLengthChange": false,
	"bFilter": true,
	"bSort": false,
	"bInfo": false,
	"bAutoWidth": false
	});
	
	/*===================
	SYNTAX HIGHLIGHTER
	===================*/			
	$('.sh-html').sourcerer('html');
			
	/*===================
	TOOLTIP
	===================*/
	$(".textips").tipTip({
		edgeOffset: 1,
		defaultPosition: 'top'
	});
	$(".action-icons").tipTip({
		edgeOffset: 1,
		defaultPosition: 'top'
	});
	$(".settings-toggle").tipTip({
		edgeOffset: 1,
		defaultPosition: 'right'
	});
	$(".right-toggle").tipTip({
		edgeOffset: 1,
		defaultPosition: 'left'
	});
	$(".tip-top").tipTip({
		edgeOffset: 1,
		defaultPosition: 'left'
	});
	$(".w-count a").tipTip({
		edgeOffset: 1,
		defaultPosition: 'top'
	});
	$(".shortcut-panel li a").tipTip({
		edgeOffset: 1,
		defaultPosition: 'left'
	});
	
	/*======================
	COLLAPSIBLE PANEL STYLE
	========================*/
	$.collapsible(".collapse-bar");
	
	/*======================
	ACCORDION MENU
	========================*/
	$('.menu').initMenu();

	/*======================
	RIGHT SLIDE BAR
	========================*/	
	$('#panel-right').slideMnu();
			
	/*======================
	TOP SWITCHBOARD
	========================*/
	$('#shortcur-bar').sortable({
		items: 'li' ,
		placeholder:'drag-place'
		})
	$( '#shortcur-bar').disableSelection();

	/*======================
	DATE PICKER
	========================*/
	 /*--Datepicker--*/
	$(".datepicker").datepicker({
		showButtonPanel: true
	});
	
		
	/*======================
	SELECT BOX
	========================*/
	$(".chzn-select").chosen();
	$(".chzn-select-deselect").chosen({
		allow_single_deselect: true
	});
	
	/*======================
	INPUT UNIFROM
	========================*/
	/*--Input files style--*/
	
	 $(".input-uniform input[type=file],.input-uniform input[type=radio],.input-uniform input[type=checkbox], input[type=file]").uniform();

	/*======================
	UI COMBOBOX
	========================*/
	$( "#combobox" ).combobox();
		  
	
	/*======================
	TEXT EDITOR
	========================*/
	/*--LARGE--*/
	$('textarea.tinymceS').tinymce({
		  // Location of TinyMCE script
		  script_url: 'js/tiny_mce/tiny_mce.js',
	
		  // Example content CSS (should be your site CSS)
		  content_css: "css/editor-styles.css",
	
		  // General options
		  theme: "advanced",
		  theme_advanced_toolbar_location: "top",
		  theme_advanced_toolbar_align: "left",
		  theme_advanced_statusbar_location: "bottom",
		  theme_advanced_resizing: false,
	
	
	  });
	  
	  /*--SIMPLE--*/
	  $('textarea.tinymce-simple').tinymce({
		  // Location of TinyMCE script
		  script_url: 'js/tiny_mce/tiny_mce.js',
	
		  // General options
		  theme: "simple",
		  theme_advanced_resizing: false,
	
	
	  });
	  
	  /*--ADVANCED--*/
	  
	  $('textarea.tinymce-adv').tinymce({
	// Location of TinyMCE script
		  script_url: 'js/tiny_mce/tiny_mce.js',
	
		  // Example content CSS (should be your site CSS)
		  content_css: "css/editor-styles.css",
	
		  // General options
		  theme: "advanced",
		  plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
	
		  // Theme options
		  theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		  theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		  theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		  theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
		  theme_advanced_toolbar_location : "top",
		  theme_advanced_toolbar_align : "left",
		  theme_advanced_statusbar_location : "bottom",
		  theme_advanced_resizing : false,
		  theme_advanced_toolbar_location: "top",
		  theme_advanced_toolbar_align: "left",
		  theme_advanced_statusbar_location: "bottom",
		  theme_advanced_resizing: false,
	
	
	});
	  
				/*--Rating--*/
				$('#number').raty({
                cancel: true,
                cancelOff: 'cancel-off-big.png',
                cancelOn: 'cancel-on-big.png',
                half: true,
                size: 24,
                starHalf: 'star-half-big.png',
                starOff: 'star-off-big.png',
                starOn: 'star-on-big.png'
            });
			
			
		
	/*======================
	BREADCRUMB
	========================*/
	$("#breadCrumb0").jBreadCrumb();
	$("#breadCrumb1").jBreadCrumb();
	$("#breadCrumb2").jBreadCrumb();
	$("#breadCrumb3").jBreadCrumb();

	/*======================
	iBUTTON Radio/Check Box
	========================*/
	
	$(".cb-enable").click(function(){
		  var parent = $(this).parents('.switch');
		  $('.cb-disable',parent).removeClass('selected');
		  $(this).addClass('selected');
		  $('.checkbox',parent).attr('checked', true);
	});
	
	$(".cb-disable").click(function(){
		var parent = $(this).parents('.switch');
		$('.cb-enable',parent).removeClass('selected');
		$(this).addClass('selected');
		$('.checkbox',parent).attr('checked', false);
	});
	
	/*======================
	 SLIDER UI
	========================*/
	// setup master volume
	$( "#master" ).slider({
		value: 60,
		orientation: "horizontal",
		range: "min",
		animate: true
	});
	// setup graphic EQ
	$( "#eq > span" ).each(function() {
		// read initial values from markup and remove that
		var value = parseInt( $( this ).text(), 10 );
		$( this ).empty().slider({
			value: value,
			range: "min",
			animate: true,
			orientation: "vertical"
		});
	
	
	$( "#slider-range" ).slider({
		range: true,
		min: 0,
		max: 500,
		values: [ 75, 300 ],
		slide: function( event, ui ) {
			$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
		}
	});
	$( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
		" - $" + $( "#slider-range" ).slider( "values", 1 ) );
	});
	
	$( "#slider-range-min" ).slider({
		range: "min",
		value: 37,
		min: 1,
		max: 700,
		slide: function( event, ui ) {
			$( "#amount2" ).val( "$" + ui.value );
		}
	});
	$( "#amount2" ).val( "$" + $( "#slider-range-min" ).slider( "value" ) );
		
	
	
	/*======================
	FILE EXPLORER
	========================*/

	var f = $('#finder').elfinder({
				url : 'connectors/php/connector.php',
				lang : 'en',
				docked : false

				// dialog : {
				// 	title : 'File manager',
				// 	height : 500
				// }

				// Callback example
				//editorCallback : function(url) {
				//	if (window.console && window.console.log) {
				//		window.console.log(url);
				//	} else {
				//		alert(url);
				//	}
				//},
				//closeOnEditorCallback : true	
				});
				
	/*======================
	CALENDAR
	========================*/
					
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
	
	$('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		editable: true,
		events: [
			{
				title: 'All Day Event',
				start: new Date(y, m, 1)
			},
			{
				title: 'Long Event',
				start: new Date(y, m, d-5),
				end: new Date(y, m, d-2)
			},
			{
				id: 999,
				title: 'Repeating Event',
				start: new Date(y, m, d-3, 16, 0),
				allDay: false
			},
			{
				id: 999,
				title: 'Repeating Event',
				start: new Date(y, m, d+4, 16, 0),
				allDay: false
			},
			{
				title: 'Meeting',
				start: new Date(y, m, d, 10, 30),
				allDay: false
			},
			{
				title: 'Lunch',
				start: new Date(y, m, d, 12, 0),
				end: new Date(y, m, d, 14, 0),
				allDay: false
			},
			{
				title: 'Birthday Party',
				start: new Date(y, m, d+1, 19, 0),
				end: new Date(y, m, d+1, 22, 30),
				allDay: false
			},
			{
				title: 'Click for Google',
				start: new Date(y, m, 28),
				end: new Date(y, m, 29),
				url: 'http://google.com/'
			}
		]
	});
	
	
	
	});
/*--Document ready function End Here--*/

	/*======================
	TREEVIEW
	========================*/

	$(function() {
		$("#tree").treeview({
			collapsed: true,
			animated: "fast",
			control:"#sidetreecontrol",
			prerendered: true,
			persist: "location"
		});
	})

	/*======================
	jQuery MODAL
	========================*/
		
	jQuery(function ($) {
	// Load dialog on page load
	//$('#basic-modal-content').modal();

	// Load dialog on click
	$('#basic-modal .basic').click(function (e) {
	$('#basic-modal-content').modal();

		return false;
		});
	});

	/*======================
	CONFIRM DIALOG
	========================*/

	jQuery(function ($) {
		$('#confirm-dialog input.confirm, #confirm-dialog a.confirm').click(function (e) {
			e.preventDefault();
	
			// example of calling the confirm function
			// you must use a callback function to perform the "yes" action
			confirm("Continue to the SimpleModal Project page?", function () {
				window.location.href = 'http://www.ericmmartin.com/projects/simplemodal/';
			});
		});
	});






	/*
	 * SimpleModal OSX Style Modal Dialog
	 * http://www.ericmmartin.com/projects/simplemodal/
	 * http://code.google.com/p/simplemodal/
	 *
	 * Copyright (c) 2010 Eric Martin - http://ericmmartin.com
	 *
	 * Licensed under the MIT license:
	 *   http://www.opensource.org/licenses/mit-license.php
	 *
	 * Revision: $Id: osx.js 238 2010-03-11 05:56:57Z emartin24 $
	 */
	
	jQuery(function ($) {
		var OSX = {
			container: null,
			init: function () {
				$("input.osx, a.osx").click(function (e) {
					e.preventDefault();	
	
					$("#osx-modal-content").modal({
						overlayId: 'osx-overlay',
						containerId: 'osx-container',
						closeHTML: null,
						minHeight: 80,
						opacity: 65, 
						position: ['0',],
						overlayClose: true,
						onOpen: OSX.open,
						onClose: OSX.close
					});
				});
			},
			open: function (d) {
				var self = this;
				self.container = d.container[0];
				d.overlay.fadeIn('slow', function () {
					$("#osx-modal-content", self.container).show();
					var title = $("#osx-modal-title", self.container);
					title.show();
					d.container.slideDown('slow', function () {
						setTimeout(function () {
							var h = $("#osx-modal-data", self.container).height()
								+ title.height()
								+ 20; // padding
							d.container.animate(
								{height: h}, 
								200,
								function () {
									$("div.close", self.container).show();
									$("#osx-modal-data", self.container).show();
								}
							);
						}, 300);
					});
				})
			},
			close: function (d) {
				var self = this; // this = SimpleModal object
				d.container.animate(
					{top:"-" + (d.container.height() + 20)},
					500,
					function () {
						self.close(); // or $.modal.close();
					}
				);
			}
		};
	
		OSX.init();
	
	});

	function confirm(message, callback) {
		$('#confirm').modal({
			closeHTML: "<a href='#' title='Close' class='modal-close'>x</a>",
			position: ["20%",],
			overlayId: 'confirm-overlay',
			containerId: 'confirm-container', 
			onShow: function (dialog) {
				var modal = this;
	
				$('.message', dialog.data[0]).append(message);
	
				// if the user clicks "yes"
				$('.yes', dialog.data[0]).click(function () {
					// call the callback
					if ($.isFunction(callback)) {
						callback.apply();
					}
					// close the dialog
					modal.close(); // or $.modal.close();
				});
			}
		});
	}




	/*======================
	IPHONE STYLE BUTTON
	========================*/

	jQuery(function ($) {

  $('.on_off :checkbox').iphoneStyle();
		$('.disabled :checkbox').iphoneStyle();
		$('.long_tiny :checkbox').iphoneStyle({ checkedLabel: 'Very Long Text', uncheckedLabel: 'Tiny' });
		
		var onchange_checkbox = ($('.onchange :checkbox')).iphoneStyle({
		  onChange: function(elem, value) { 
			$('span#status').html(value.toString());
		  }
		});
	   });
	   
	/*======================
	jQuery FLOT CHART
	========================*/
		 
	$(function () {
		var sin = [], cos = [];
		for (var i = 0; i < 14; i += 0.5) {
			sin.push([i, Math.sin(i)]);
			cos.push([i, Math.cos(i)]);
		}
	
		var plot = $.plot($("#placeholder"),
			   [ { data: sin, label: "sin(x)"}, { data: cos, label: "cos(x)" } ], {
				   series: {
					   lines: { show: true },
					   points: { show: true }
				   },
				   grid: { hoverable: true, clickable: true },
				   yaxis: { min: -1.2, max: 1.2 }
				 });
	
		function showTooltip(x, y, contents) {
			$('<div id="tooltip">' + contents + '</div>').css( {
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 5,
				border: '1px solid #fdd',
				padding: '2px',
				'background-color': '#fee',
				opacity: 0.80
			}).appendTo("body").fadeIn(200);
		}
	
		var previousPoint = null;
		$("#placeholder").bind("plothover", function (event, pos, item) {
			$("#x").text(pos.x.toFixed(2));
			$("#y").text(pos.y.toFixed(2));
	
			
				if (item) {
					if (previousPoint != item.dataIndex) {
						previousPoint = item.dataIndex;
						
						$("#tooltip").remove();
						var x = item.datapoint[0].toFixed(2),
							y = item.datapoint[1].toFixed(2);
						
						showTooltip(item.pageX, item.pageY,
									item.series.label + " of " + x + " = " + y);
					}
				}
				else {
					$("#tooltip").remove();
					previousPoint = null;            
				}
	
		});
		}); 


	$(function () {
		var datasets = {
			"usa": {
				label: "USA",
				data: [[1988, 483994], [1989, 479060], [1990, 457648], [1991, 401949], [1992, 424705], [1993, 402375], [1994, 377867], [1995, 357382], [1996, 337946], [1997, 336185], [1998, 328611], [1999, 329421], [2000, 342172], [2001, 344932], [2002, 387303], [2003, 440813], [2004, 480451], [2005, 504638], [2006, 528692]]
			},        
			"russia": {
				label: "Russia",
				data: [[1988, 218000], [1989, 203000], [1990, 171000], [1992, 42500], [1993, 37600], [1994, 36600], [1995, 21700], [1996, 19200], [1997, 21300], [1998, 13600], [1999, 14000], [2000, 19100], [2001, 21300], [2002, 23600], [2003, 25100], [2004, 26100], [2005, 31100], [2006, 34700]]
			},
			"uk": {
				label: "UK",
				data: [[1988, 62982], [1989, 62027], [1990, 60696], [1991, 62348], [1992, 58560], [1993, 56393], [1994, 54579], [1995, 50818], [1996, 50554], [1997, 48276], [1998, 47691], [1999, 47529], [2000, 47778], [2001, 48760], [2002, 50949], [2003, 57452], [2004, 60234], [2005, 60076], [2006, 59213]]
			},
			"germany": {
				label: "Germany",
				data: [[1988, 55627], [1989, 55475], [1990, 58464], [1991, 55134], [1992, 52436], [1993, 47139], [1994, 43962], [1995, 43238], [1996, 42395], [1997, 40854], [1998, 40993], [1999, 41822], [2000, 41147], [2001, 40474], [2002, 40604], [2003, 40044], [2004, 38816], [2005, 38060], [2006, 36984]]
			},
			"denmark": {
				label: "Denmark",
				data: [[1988, 3813], [1989, 3719], [1990, 3722], [1991, 3789], [1992, 3720], [1993, 3730], [1994, 3636], [1995, 3598], [1996, 3610], [1997, 3655], [1998, 3695], [1999, 3673], [2000, 3553], [2001, 3774], [2002, 3728], [2003, 3618], [2004, 3638], [2005, 3467], [2006, 3770]]
			},
			"sweden": {
				label: "Sweden",
				data: [[1988, 6402], [1989, 6474], [1990, 6605], [1991, 6209], [1992, 6035], [1993, 6020], [1994, 6000], [1995, 6018], [1996, 3958], [1997, 5780], [1998, 5954], [1999, 6178], [2000, 6411], [2001, 5993], [2002, 5833], [2003, 5791], [2004, 5450], [2005, 5521], [2006, 5271]]
			},
			"norway": {
				label: "Norway",
				data: [[1988, 4382], [1989, 4498], [1990, 4535], [1991, 4398], [1992, 4766], [1993, 4441], [1994, 4670], [1995, 4217], [1996, 4275], [1997, 4203], [1998, 4482], [1999, 4506], [2000, 4358], [2001, 4385], [2002, 5269], [2003, 5066], [2004, 5194], [2005, 4887], [2006, 4891]]
			}
		};
	
		// hard-code color indices to prevent them from shifting as
		// countries are turned on/off
		var i = 0;
		$.each(datasets, function(key, val) {
			val.color = i;
			++i;
		});
		
		// insert checkboxes 
		var choiceContainer = $("#choices");
		$.each(datasets, function(key, val) {
			choiceContainer.append('<br/><input type="checkbox" name="' + key +
								   '" checked="checked" id="id' + key + '">' +
								   '<label for="id' + key + '">'
									+ val.label + '</label>');
		});
		choiceContainer.find("input").click(plotAccordingToChoices);
	
		
		function plotAccordingToChoices() {
			var data = [];
	
			choiceContainer.find("input:checked").each(function () {
				var key = $(this).attr("name");
				if (key && datasets[key])
					data.push(datasets[key]);
			});
	
			if (data.length > 0)
				$.plot($("#visitors"), data, {
					yaxis: { min: 0 },
					xaxis: { tickDecimals: 0 }
				});
		}
	
		plotAccordingToChoices();
	});



	$(function () {
		// data
		var data = [
			{ label: "Series1",  data: 10},
			{ label: "Series2",  data: 30},
			{ label: "Series3",  data: 90},
			{ label: "Series4",  data: 70},
			{ label: "Series5",  data: 80},
			{ label: "Series6",  data: 110}
		];
	
		
		// GRAPH
		$.plot($("#graph"), data, 
		{
			series: {
				pie: { 
					show: true,
					radius: 1,
					label: {
						show: true,
						radius: 1,
						formatter: function(label, series){
							return '<div class="series-label">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
						},
						background: { opacity: 0.8 }
					}
				}
			},
			legend: {
				show: false
			}
		});
		
		// GRAPH 3
		$.plot($("#graph3"), data, 
		{
			series: {
				pie: { 
					show: true,
					radius: 1,
					label: {
						show: true,
						radius: 3/4,
						formatter: function(label, series){
							return '<div class="series-label">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
						},
						background: { opacity: 0.5 }
					}
				}
			},
			legend: {
				show: false
			}
		});
		
		$.plot($("#graph4"), data, 
		{
			series: {
				pie: { 
					show: true,
					radius: 1,
					label: {
						show: true,
						radius: 3/4,
						formatter: function(label, series){
							return '<div class="series-label">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
						},
						background: { opacity: 0.5 }
					}
				}
			},
			legend: {
				show: false
			}
		});
		
		$.plot($("#graph8"), data,
	{
			series: {
				pie: {
					show: true,
					radius:300,
					label: {
						show: true,
						formatter: function(label, series){
							return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
						},
						threshold: 0.1
					}
				}
			},
			legend: {
				show: false
			}
	});
		
		$.plot($("#graph9"), data,
	{
			series: {
				pie: {
					show: true,
					radius: 1,
					tilt: 0.5,
					label: {
						show: true,
						radius: 1,
						formatter: function(label, series){
							return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
						},
						background: { opacity: 0.8 }
					},
					combine: {
						color: '#999',
						threshold: 0.1
					}
				}
			},
			legend: {
				show: false
			}
	});
		
		// DONUT
		$.plot($("#donut"), data, 
		{
			series: {
				pie: { 
					innerRadius: 0.5,
					show: true
				}
			}
		});
	
		});




