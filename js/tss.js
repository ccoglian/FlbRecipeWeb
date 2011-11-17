/* 
 * Main js function repository.
 *
 * @author ccoglianese
 */

$.ajaxSetup ({
    cache: false
});

$(function() {
    addHover();
    grabFocus();

    //show a spinner icon whenever we're doing ajax calls
    $('body').mousemove(function(e) {
        var ajax_waiting_symbol = document.getElementById('waiting_symbol');

        ajax_waiting_symbol.style.left = e.pageX + 5 + "px"; // put the spinner in the same spot chrome does
        ajax_waiting_symbol.style.top = e.pageY - 8 + "px";

        $.cookie('spinnerLeft', ajax_waiting_symbol.style.left, {path: '/'}); // FIXME add domain .example.com
        $.cookie('spinnerTop', ajax_waiting_symbol.style.top, {path: '/'});
    });

    $('#waiting_symbol').ajaxStart(function() {
        var ajax_waiting_symbol = document.getElementById('waiting_symbol');

        if (ajax_waiting_symbol.style.left == "0px" && $.cookie('spinnerLeft') != '') {
            ajax_waiting_symbol.style.left = $.cookie('spinnerLeft');
            ajax_waiting_symbol.style.top = $.cookie('spinnerTop');
        }
        
        $('#waiting_symbol').show();
    });

    $('#waiting_symbol').ajaxStop(function() {
        $('#waiting_symbol').hide();
    });

    if ($('#week_of_list').length) {
        state = getInitialState();
        
        load('week_of_list', '/util/getallmondays?selid=' + state['week_of']);
    }

    if ($('#student_group_list').length) {
        state = getInitialState();

        var extras = state['extra_student_group_list_params'];
        if (extras) {
            extras = "&" + extras;
        } else {
            extras = '';
        }

        load('student_group_list', '/studentgroup/getall?group_type=default&selid=' + state['student_group_id'] + extras);

        if ($('#student_list').length) {
            load('student_list', '/student/getall?header=select' + (state['student_group_id'] ? '&student_group_id=' + state['student_group_id'] : '') + '&selid=' + state['student_id']);

            if (state['student_id']) {
                load('load_div', getLoadURL(state));
            }
        } else {
            if (state['student_group_id']) {
                load('load_div', getLoadURL(state));
            }
        }

        setSessionState(state);
        replaceState(state, 'replaced state', getStatefulURL(state));
    }
    
    // ajax back button!
    window.onpopstate = function (event) {
        var state = event.state;

        if (!state) {
            return;
        }

        $.each(getStateKeys(), function(i, key) {
            var selector = getSelectorForStateKey(key);
            if ($(selector).length) {
                $(selector).val(state[key]);
            }
        });

        load('student_list', '/student/getall?header=select&student_group_id='+state['student_group_id']+'&selid='+state['student_id']);
        load('load_div', getLoadURL(state));
        setSessionState(state);
    };
});

function getStateKeys() {
    return ['student_group_id', 'student_id', 'week_of'];
}

function getStatefulURL(state, baseURL) {
    var url = '';

    if (!baseURL) {
        baseURL = getBaseURL();
    }

    $.each(getStateKeys(), function(i, key) {
        if (state[key]) {
            url = (!url ? baseURL + "?" : url + "&");
            url += key + "=" + state[key];
        }
    });

    return url;
}

function studentGroupChange(value) {
    $('.status-banner').fadeOut(100);

    var state = getCurrentState({student_group_id: value});

    if ($("#student_list").length) {
        load('student_list', '/student/getall?header=select&student_group_id='+state['student_group_id']+'&selid='+state['student_id']);
    } else {
        load('load_div', getLoadURL(state));
    }

    setSessionState(state);
    pushState(state, 'pushed state', getStatefulURL(state));
}

function pushState(state, title, url) {
    if (history.pushState) {
        history.pushState(state, title, url);
    }
}

function replaceState(state, title, url) {
    if (history.replaceState) {
        history.replaceState(state, title, url);
    }
}

function weekOfChange(value) {
    $('.status-banner').fadeOut(100);
    var state = getCurrentState({week_of: value});
    load('load_div', getLoadURL(state));
    setSessionState(state);
    pushState(state, 'pushed state', getStatefulURL(state));
}

function studentChange(value) {
    $('.status-banner').fadeOut(100);
    var state = getCurrentState({student_id: value});
    // FIXME ask if they've edited below
    load('load_div', getLoadURL(state));
    setSessionState(state);
    pushState(state, 'pushed state', getStatefulURL(state));
}

function getSelectorForStateKey(key) {
    return "#" + key.replace("_id", "") + "_list";
}

function getCurrentState(state) {
    $.each(getStateKeys(), function(i, key) {
        var selector = getSelectorForStateKey(key);
        if (!state[key] && $(selector).length) {
            state[key] = $(selector).val();
        }
    });

    return state;
}

function setSessionState(state) {
    $.post('/sessionset.php', state);
}

function isNumber(x) {
    return !isNaN(x - 0);
}

function addBusDays(startDate, numDays) {
    var sign = numDays == 0 || !isNumber(numDays) ? 1 : numDays / Math.abs(numDays);
    var oneDay = 1000 * 60 * 60 * 24 * sign;
    var endDate = new Date(startDate);

    if (!isNumber(numDays)) {
        return endDate;
    }

    // skip weekends
    // FIXME skip holiays too
    while (endDate.getDay() == 6 || endDate.getDay() == 0) {
        endDate.setTime(endDate.getTime() + oneDay);
    }

    for (i = 0; i < Math.abs(numDays); i++) {
        endDate.setTime(endDate.getTime() + oneDay);

        // skip weekends
        // FIXME skip holiays too
        while (endDate.getDay() == 6 || endDate.getDay() == 0) {
            endDate.setTime(endDate.getTime() + oneDay);
        }
    }

    return endDate;
}

function load(elementId, loadUrl) {
    // .html('<option value="0">Loading...</option>')
    $("#" + elementId).load(loadUrl);
}

function deleteRow(tableId, tr, deleteUrl) {
    $.get(deleteUrl, function(error) {
        if (error != '') {
            alert(error);
            return;
        }
       
        var row = tr.parentNode.parentNode.rowIndex;
        document.getElementById(tableId).deleteRow(row);
    });
}

function insertRowBefore(tableId, sel, studentGroupId) {
    var selectedIndex = sel.selectedIndex;
    var selectedValue = sel.options[selectedIndex].value;
    var selectedText = sel.options[selectedIndex].text

    if (selectedIndex == 0) {
        return;
    }

    $.get("/studentgroup/addmember", {student_id: selectedValue, student_group_id: studentGroupId}, function(error) {
        if (error != '') {
            alert(error);
            sel.selectedIndex = 0;
            return;
        }

        var row = sel.parentNode.parentNode.rowIndex;
        var x = document.getElementById(tableId).insertRow(row);
        var rowId = "insrow_" + selectedValue;
        var rowHTML = "<td><div class='icon-button-small icon-button ui-state-default ui-corner-all' title='Remove' onClick='deleteRow(\"student_group_members\", this, \"/studentgroup/delete?student_id="
            + selectedValue + "&student_group_id=" + studentGroupId + "\")'><span class='ui-icon ui-icon-close'></span></div></td><td><a href='../student/index?id=" + selectedValue + "'>"
            + selectedText + "</a></td>";

        x.id = rowId; // set the ID of the row so we can look it up below using jQuery
        $("#" + rowId).html(rowHTML);
        addHover();
        sel.selectedIndex = 0;
    });
}

function insertRowAfter(tableId, item, studentId, studentGroupId) {
    var tr = item.parentNode.parentNode;
    var row = tr.rowIndex;
    var table = document.getElementById(tableId);
    var x = table.insertRow(row + 1);
    var numRows = $("#" + tableId + " tr").length;
    var rowId = "insrow_" + numRows;
    var noOptions = '<option>Select</option>';
    var selectId = "demerit_type_id_" + studentId + "_" + numRows;
    var rowHTML = "<td></td><td><select id='" + selectId + "' name='" + selectId + "'>" + noOptions + "</select></td>"
        + "<td><input type='text' name='comment_" + studentId + "_" + numRows + "' class='comments'></td>"
        + "<td><div class='icon-button ui-state-default ui-corner-all' title='Add more...' onclick=\"insertRowAfter('demerit_table', this, " + studentId + ", " + studentGroupId + ")\"><span class='ui-icon ui-icon-plusthick'></span></div></td>";

    x.id = rowId;
    x.className = tr.className;
    $("#" + rowId).html(rowHTML);
    addHover();
    $("#" + selectId).load("/demerit/getall");
}

function addHover() {
    $('.icon-button').hover(
        function() {$(this).addClass('ui-state-hover');},
        function() {$(this).removeClass('ui-state-hover');}
    );
}

function grabFocus() {
    $("select:visible:first").focus();
    $("input:text:visible:first").focus();
}

function getStatusBox(title, msg, type, css) {
    var extra_style = 'background: #D8E4F1; border: solid thin #69C;';
    var icon = 'ui-icon-info';
    var ui_state = 'ui-state-highlight';

    if (type == 'error') {
        icon = 'ui-icon-alert';
        ui_state = 'ui-state-error';
        extra_style  = '';
    }

    return "<div class='" + css + " ui-widget status_box'>"
            + "<div class='" + ui_state + " ui-corner-all' style='padding: 0 .7em; " + extra_style + "'>"
                    + "<p class='" + css + "'><span class='ui-icon " + icon + "' style='float: left; margin-right: .3em;'></span>"
                    + "<strong>" + title + "</strong> " + msg + "</p>"
            + "</div>"
    + "</div>";
}

// set this as the click function for your submit button
// $('input:submit').click(formSubmit);
function formSubmit(e) {
    e.preventDefault();
    $('#main_form').trigger('submit');
    return false;
}

// set this as the submit function of your form
// $('#main_form').submit(onSubmit('/communication/add.php', '/communications', {student_group_id : <?php echo $student_group_id; ?>, student_id : <?php echo $student_id; ?>}));
function onSubmit(postURL, state) {
    return function(e) {
        $('input:submit').attr("disabled", "true");
        e.preventDefault();
        
        $.post(postURL, $("#main_form").serialize(),
            function(data) {
                if (data && data.errors) {
                    $('.status_box').hide(); // hide the old ones
                    $('.status-banner').hide(); // hide any old banner too
                    $.each(data.errors, function(key, value) {
                        $('#' + key + '_status_box').html(getStatusBox('Error:', value, 'error', 'status-small'));
                    });
                    $('input:submit').removeAttr("disabled");
                } else if (data && data.id) {
                    window.parent.$("#facebox .close").trigger('click');
                    $('#' + data.id, window.parent.document).replaceWith(data.row);
                    $('#' + data.id, window.parent.document).css('background-color', 'yellow');
                    $('#' + data.id, window.parent.document).animate({backgroundColor: "white"}, 2000);
                    $('.icon-button', window.parent.document).hover(
                        function() {$(this).addClass('ui-state-hover');},
                        function() {$(this).removeClass('ui-state-hover');}
                    );
                } else {
                    window.location.assign(getStatefulURL(state));
                }
            }, "json");
    };
}

function edit(height, width, getURL) {
    $.facebox({
        iframe: getURL,
        rev: 'iframe|' + height + '|' + width
    });
}

function activate(postURL) {
    $.post(postURL, {},
        function(data) {
            if (data && data.id) {
                $('#' + data.id, window.parent.document).replaceWith(data.row);
                $('#' + data.id, window.parent.document).css('background-color', 'yellow');
                $('#' + data.id, window.parent.document).animate({backgroundColor: "white"}, 2000);
                $('.icon-button', window.parent.document).hover(
                    function() {$(this).addClass('ui-state-hover');},
                    function() {$(this).removeClass('ui-state-hover');}
                );
            }
        }, "json");
}

function tableSorterExtraction(node) {
    if (node.id) {
        return node.id;
    }
    
    return node.innerHTML;
}

function fillInForm(key, value) {
    var rb = $('[name="' + key + '"][type="radio"][value="' + value + '"]');
    var dp = $('[name="' + key + '"].hasDatepicker');

    if (rb.length) {
        rb.attr('checked', true);
    } else if (dp.length) {
        var date = $.datepicker.parseDate("yy-m-d", value);
        dp.val($.datepicker.formatDate("m/d/yy", date));
    } else {
        $('[name="' + key + '"]').val(value);
    }
}