/*****************************************************************************
*
*	jQuery 날짜 선택 플러그인
*
*	- creator : kwangmyung, choi(mycastor@gmail.com)
*	- created date : 2009.09.02
*   - last modified : 2009.09.02
*
******************************************************************************/
//
//	< How to use >
//  <input type="text" id="date" name="date" onclick="$.datePicker.show(this.id, true)" />
//
(function ($) {
	var x, y;
	var setYear, setMonth, setDay;
	var ele_id;
	var hyphen = true;
	var c = function () { };

	$.datePicker =  {
		show : function (obj_id, useHyphen, callback) {
			var offset = $('#' + obj_id).offset();

			x = offset.left;
			y = offset.top;

			ele_id = obj_id;
			hyphen = useHyphen;
			c = callback;

			init();
			drawCalendar(setYear, setMonth, setDay);
		},

		next : function (obj_id) {
			ele_id = obj_id;

			setNextMonth();
			drawCalendar(setYear, setMonth, setDay);
		},

		prev : function (obj_id) {
			ele_id = obj_id;

			setPrevMonth();
			drawCalendar(setYear, setMonth, setDay);
		},

		dispatchValue : function (year, month, day) {
			var returnVal = "";

			if(parseInt(month) < 10)
				month = "0".concat(month);

			if(parseInt(day) < 10)
				day = "0".concat(day);

			if(hyphen) 
				returnVal = returnVal.concat(year + "-" + month + "-" + day);
			else
				returnVal = returnVal.concat(year + month + day);

			$("#".concat(ele_id)).val(returnVal);
			this.hide();
			//c();
		},

		hide : function () {
			$("#datePicker").remove();
		}
	};

	function init() {
		var date = new Date();

		setYear = (navigator.appName.indexOf("Microsoft Internet Explorer") > -1) ? date.getYear() : date.getYear() + 1900;
		setMonth = date.getMonth() + 1;
		setDay = date.getDate();
	}

	function setNextMonth() {
		if(setMonth > 11) {
			setYear++;
			setMonth = 1;
		} else {
			setMonth++;
		}
	};

	function setPrevMonth() {
		if(setMonth < 2) {
			setYear--;
			setMonth = 12;
		} else {
			setMonth--;
		}
	};

	function getLastDay(year, month) {
		var date = new Date();
		var cYear, cMonth, lastDay;

		for(var i = 1; i < 32; i++) {
			date.setYear(year);
			date.setMonth(month - 1);
			date.setDate(i);

			cYear = (navigator.appName.indexOf("Microsoft Internet Explorer") > -1) ? date.getYear() : date.getYear() + 1900;
			cMonth = date.getMonth() + 1;

			if(year == cYear && month == cMonth)
				lastDay = i;
		}

		return lastDay;
	};

	function drawCalendar(year, month, day) {
		var lastDay = getLastDay(year, month);
		var weekDay = new Date(year, month - 1, 1).getDay();

		var currDate = new Date();
		var currYear = currDate.getYear();
		var currMonth = currDate.getMonth() + 1;
		var currDay = currDate.getDate();

		var dispDate = year + ".";

		if(parseInt(month) < 10)
			dispDate = dispDate.concat("0" + month);
		else
			dispDate = dispDate.concat(month);

		var html = "<div id=\"datePicker\" style=\"position:absolute\">";

		html = html.concat("<table border=\"0\" width=\"189\">");
		html = html.concat("<tr height=\"28\">");
		html = html.concat("<td colspan=\"2\"><a href=\"javascript:$.datePicker.prev('" + ele_id + "')\">←</a></td>");
		html = html.concat("<td colspan=\"3\">" + dispDate + "</td>");
		html = html.concat("<td colspan=\"2\"><a href=\"javascript:$.datePicker.next('" + ele_id + "')\">→</a></td>");
		html = html.concat("</tr>");
		html = html.concat("<tr>");
		html = html.concat("<td width=\"27\" class=\"fntRed\">SUN</td>");
		html = html.concat("<td width=\"27\">MON</td>");
		html = html.concat("<td width=\"27\">TUE</td>");
		html = html.concat("<td width=\"27\">WED</td>");
		html = html.concat("<td width=\"27\">THI</td>");
		html = html.concat("<td width=\"27\">FRI</td>");
		html = html.concat("<td width=\"27\" class=\"fntBlue\">SAT</td>");
		html = html.concat("</tr>");

		var i, j = 1, k, box = 0;

		for(i = 0; i < weekDay; i++) {
			if(i < 1)
				html = html.concat("<tr>");

			html = html.concat("<td width=\"27\">&nbsp;</td>");
			box++;
		}

		while(j <= lastDay) {
			if(currYear == year && currMonth == month && currDay == j)
				html = html.concat("<td width=\"27\" onclick=\"$.datePicker.dispatchValue('" + year + "', '" + month + "', '" + j + "')\"><strong>" + j + "</strong></td>");
			else
				html = html.concat("<td width=\"27\" onclick=\"$.datePicker.dispatchValue('" + year + "', '" + month + "', '" + j + "')\">" + j + "</td>");

			if(box < 6) {
				box++;
			} else {
				html = html.concat("</tr>");

				if(j < lastDay)
					html = html.concat("<tr>");

				box = 0;			
			}

			j++;
		}

		if(box > 0) {
			for(k = box; k < 7; k++) {
				html = html.concat("<td width=\"27\">&nbsp;</td>");

				if(k > 6)
					html = html.concat("</tr>");
			}
		}

		html = html.concat("</table>");
		html = html.concat("</div>");
	
		$.datePicker.hide();
		$(document.body).append(html);

		$("#datePicker").css("left", x);
		$("#datePicker").css("top", y);
		$("#datePicker > table").css("border", "1px solid #4D4D4D");
		$("#datePicker > table").css("border-collapse", "collapse");
		$("#datePicker > table > * > tr:first > td:eq(1)").css("font-weight", "bold");
		$("#datePicker > table > * > tr > td").css("background-color", "#FFFFFF");
		$("#datePicker > table > * > tr > td").css("font-family", "tahoma");
		$("#datePicker > table > * > tr > td").css("font-size", "8pt");
		$("#datePicker > table > * > tr > td").css("color", "#4D4D4D");
		$("#datePicker > table > * > tr > td").css("text-align", "center");
		$("#datePicker > table > * > tr > td.fntRed").css("color", "red");
		$("#datePicker > table > * > tr > td.fntBlue").css("color", "blue");
		$("#datePicker > table > * > tr:gt(0) > td").css("border", "1px solid #4D4D4D");
		$("#datePicker > table > * > tr:gt(1) > td").css("cursor", "hand");
	};
}) (jQuery);