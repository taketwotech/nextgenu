/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2018 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */

define(["jquery","./tether","./babel-external-helpers","./breakpoints"],function(jQuery,Tether,babelHelpers,breakpoints){window.jQuery=jQuery,window.Tether=Tether,window.babelHelpers=babelHelpers,require(["theme_remui/bootstrap","theme_remui/jquery-mousewheel","theme_remui/jquery-asScrollbar","theme_remui/jquery-asScrollable","theme_remui/jquery-asHoverScroll","theme_remui/screenfull","theme_remui/jquery-slidePanel","theme_remui/State","theme_remui/Base","theme_remui/Plugin","theme_remui/Config","theme_remui/Menubar","theme_remui/Sidebar","theme_remui/PageAside","theme_remui/menu","theme_remui/asscrollable","theme_remui/slidepanel","theme_remui/skintools","theme_remui/RemUI"],function(BS,mw,asb,asbl,ahs,sfl,spnl,State,Base,Plugin,Config,Menubar,Sidebar,PageAside,menu,Scrollable,SlidePanel,skintools,RemUI){function changeViewToggler(par){var activeClass="list_btn",passiveClass="grid_btn",removeClass="col-lg-3 col-md-6 gridview",addClass="col-lg-12 col-md-12 listview",state="list",addimgClass="listStyle",removeimgClass="gridStyle",addcardfooter="list-activity-buttons",addprogress="list-progress";0==par?(activeClass="grid_btn",passiveClass="list_btn",removeClass="col-lg-12 col-md-12 listview",addClass="col-lg-3 col-md-6 gridview",state="grid",addimgClass="gridStyle",removeimgClass="listStyle",jQuery(".activity-btn").removeClass(addcardfooter),jQuery(".progress.progress-xs").removeClass(addprogress),addprogress="",addcardfooter="",jQuery(".card-footer > a").removeClass("start-course-list")):jQuery(".card-footer > a").addClass("start-course-list"),jQuery("."+passiveClass).removeClass("active"),jQuery("."+activeClass).addClass("active"),jQuery("#categoryCourses").children("div").removeClass(removeClass),jQuery("#categoryCourses").children("div").addClass(addClass),jQuery("#mycategoryCourses").children("div").removeClass(removeClass),jQuery("#mycategoryCourses").children("div").addClass(addClass),jQuery(".instructor-img").removeClass(removeimgClass),jQuery(".instructor-img").addClass(addimgClass),jQuery(".activity-btn").addClass(addcardfooter),jQuery(".progress.progress-xs").addClass(addprogress),M.util.set_user_preference("course_view_state",state,null)}RemUI.run(),jQuery(document).ready(function(){var url_string=window.location.href;1==new URL(url_string).searchParams.get("mycourses")?(jQuery("a[href='#mycoursestab']").trigger("click"),jQuery("a[href='#coursestab']").removeClass("active"),jQuery("#coursestab").removeClass("active"),jQuery("a[href='#mycoursestab']").addClass("active"),jQuery("#mycoursestab").addClass("active")):(jQuery("a[href='#coursestab']").trigger("click"),jQuery("a[href='#mycoursestab']").removeClass("active"),jQuery("#mycoursestab").removeClass("active"),jQuery("a[href='#coursestab']").addClass("active"),jQuery("#coursestab").addClass("active"))}),jQuery(".grid_btn").click(function(){changeViewToggler(!1)}),jQuery(".list_btn").click(function(){changeViewToggler(!0)}),jQuery('a[aria-controls="coursestab"]').click(function(){jQuery("#coursesearch2 .coursesearchbox #mycoursesfield").remove()}),jQuery('a[aria-controls="mycoursestab"]').click(function(){jQuery("#coursesearch2 .coursesearchbox ").prepend('<input id="mycoursesfield" type="hidden" value="1" name="mycourses"/>')})})});