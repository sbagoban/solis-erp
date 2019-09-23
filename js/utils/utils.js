
//un-dock windows
function setUnDock(splitObject, vCell, w, h) {
    var windowObject = splitObject.dhxWins.window(vCell);
    windowObject.setDimension(w, h);
    windowObject.center();
    windowObject.denyResize();
    windowObject.setModal(true);
    windowObject.button("Park").hide();
    windowObject.button("minmax1").hide();
    windowObject.button("dock").hide();
    windowObject.button("close").show();

    windowObject.attachEvent("onClose", function () {
        windowObject.setModal(false);
    });

}

//progress bar
function progress_bar(task) {
    var dhxWins_progress_bar = null;

    dhxWins_progress_bar = new dhtmlXWindows();
    global_progress_bar_win = dhxWins_progress_bar.createWindow("win_pb", 10, 10, 430, 155);
    dhxWins_progress_bar.window("win_pb").setModal(true);
    global_progress_bar_win.setIcon('');
    global_progress_bar_win.setText(task);
    global_progress_bar_win.denyResize();
    global_progress_bar_win.denyPark();
    global_progress_bar_win.button("minmax1").hide();
    global_progress_bar_win.button("park").hide();
    global_progress_bar_win.button("close").hide();
    global_progress_bar_win.centerOnScreen();
    var layout_win = global_progress_bar_win.attachLayout('2E');
    var cell_a = layout_win.cells('a');
    var cell_b = layout_win.cells('b');

    var img = new Image();
    img.src = "images/progress_bar.gif";
    cell_a.attachURL(img.src);
    cell_a.hideHeader();

    cell_b.hideHeader();
    cell_b.setHeight(30);

    cell_b.attachURL("php/utils/timer.php");

    dhxWins_progress_bar.attachEvent("onClose", function () {
        dhxWins_progress_bar.window("win_pb").setModal(false);
        global_progress_bar_win.hide();
        cell_b.detachObject();
        cell_b = null;
    });
}

/*Removes removeChar from string input inputString */
function utils_trim(inputString, removeChar)
{
    //trim(variablename," ")
    if (!inputString)
    {
        inputString = "";
    }

    inputString = inputString.toString();

    var returnString = inputString;

    if (removeChar.length)
    {
        while ('' + returnString.charAt(0) == removeChar)
        {
            returnString = returnString.substring(1, returnString.length);
        }
        while ('' + returnString.charAt(returnString.length - 1) == removeChar)
        {
            returnString = returnString.substring(0, returnString.length - 1);
        }
    }

    return returnString;
}



/*Returns the date obj of a date string. dtStr: date string passed in; dtFormatIn: the format in which the date string is*/
function utils_createDateObjFromString(dtStr, dtFormatIn)
{

    if (dtFormatIn == "yyyy-mm-dd")
    {
        var arr = dtStr.split("-");
        var yr = parseInt(arr[0], 10);
        var month = parseInt(arr[1], 10) - 1; //months start with 0 in js
        var day = parseInt(arr[2], 10);

        return new Date(yr, month, day);
    } else if (dtFormatIn == "dd-mm-yyyy")
    {
        var arr = dtStr.split("-");
        var yr = parseInt(arr[2], 10);
        var month = parseInt(arr[1], 10) - 1; //months start with 0 in js
        var day = parseInt(arr[0], 10);

        return new Date(yr, month, day);
    }

    return null; //no recognised format
}

function utils_addCommas(nStr)
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function utils_parseInt(val)
{
    if (val == "")
    {
        return 0;
    }

    if (isNaN(val))
    {
        return 0;
    }

    return parseInt(val, 10);
}


//adds all values in a grid column
function utils_sumColumn(grid, ind) {
    var out = 0;
    for (var i = 0; i < grid.getRowsNum(); i++) {
        out += parseFloat(grid.cells2(i, ind).getValue());
    }
    return out;
}

function imageExists(url, callback) {
    var img = new Image();
    img.onload = function () {
        callback(true);
    };
    img.onerror = function () {
        callback(false);
    };
    img.src = url;
}

function utils_formatDate(dt, format)
{
    if (dt == "")
    {
        return "";
    }

    //dt is string in YYYY-mm-dd format

    var dte_arr = dt.split("-");
    var dte_obj = new Date(dte_arr[0], dte_arr[1] - 1, dte_arr[2]);

    if (format == "DAY DATE MONTH YEAR")
    {
        return global_weekday[dte_obj.getDay()] + ", " + dte_obj.getDate() + " " + global_months[dte_obj.getMonth()] + " " + dte_obj.getFullYear();
    } else if (format == "DATE MON YEAR")
    {
        return dte_obj.getDate() + " " + global_months_abrv[dte_obj.getMonth()] + " " + dte_obj.getFullYear();
    } else if (format == "DD-MM-YYYY")
    {
        return dte_arr[2] + "-" + dte_arr[1] + "-" + dte_arr[0];
    } else if (format == "DD/MM/YYYY")
    {
        return dte_arr[2] + "/" + dte_arr[1] + "/" + dte_arr[0];
    } else if (format == "DATE MON YY")
    {
        return dte_obj.getDate() + " " + global_months_abrv[dte_obj.getMonth()] + " " + dte_obj.getFullYear().toString().substr(-2);
    }

    return dt;
}

function utils_str_to_date(str)
{
    //str in YYYY-mm-dd format
    var dte_arr = str.split("-");
    dte_arr = new Date(dte_arr[0], dte_arr[1] - 1, dte_arr[2]);
    return dte_arr;
}

function utils_date_to_str(dt)
{
    //dt is a date object and return in format YYYY-mm-dd
    return dt.getFullYear() + "-" + utils_pad((dt.getMonth() + 1), 2) + "-" + utils_pad(dt.getDate(), 2);
}

function utils_pad(num, size) {
    var s = num + "";
    while (s.length < size)
        s = "0" + s;
    return s;
}

function utils_clone(obj) {
    var copy;

    // Handle the 3 simple types, and null or undefined
    if (null == obj || "object" != typeof obj)
        return obj;

    // Handle Date
    if (obj instanceof Date) {
        copy = new Date();
        copy.setTime(obj.getTime());
        return copy;
    }

    // Handle Array
    if (obj instanceof Array) {
        copy = [];
        for (var i = 0, len = obj.length; i < len; i++) {
            copy[i] = clone(obj[i]);
        }
        return copy;
    }

    // Handle Object
    if (obj instanceof Object) {
        copy = {};
        for (var attr in obj) {
            if (obj.hasOwnProperty(attr))
                copy[attr] = clone(obj[attr]);
        }
        return copy;
    }

    throw new Error("Unable to copy obj! Its type isn't supported.");
}
function utils_validateTimeOrder(tmfrom, tmto)
{
    //format must be in HH:mm
    //returns true if tmfrom <= tmto, false otherwise

    tmfrom = tmfrom.split(":");
    tmto = tmto.split(":");

    var HHFrom = tmfrom[0];
    var mmFrom = tmfrom[1];

    var HHTo = tmto[0];
    var mmTo = tmto[1];

    if (parseInt(HHFrom, 10) > parseInt(HHTo, 10))
    {
        return false;
    }

    if (parseInt(mmFrom, 10) > parseInt(mmTo, 10) && parseInt(HHFrom, 10) == parseInt(HHTo, 10))
    {
        return false;
    }
    return true;
}


function utils_validateDateOrder(dtfrom, dtto)
{
    //format must be in dd-mm-yyyy
    //returns true if dtfrom <= dtto, false otherwise

    dtfrom = dtfrom.split("-");
    dtto = dtto.split("-");

    var ddFrom = dtfrom[0];
    var mmFrom = dtfrom[1];
    var yyFrom = dtfrom[2];

    var ddTo = dtto[0];
    var mmTo = dtto[1];
    var yyTo = dtto[2];

    if (parseInt(yyFrom, 10) > parseInt(yyTo, 10))
    {
        return false;
    }

    if (parseInt(mmFrom, 10) > parseInt(mmTo, 10) && parseInt(yyFrom, 10) == parseInt(yyTo, 10))
    {
        return false;
    }

    if (parseInt(ddFrom, 10) > parseInt(ddTo, 10) && parseInt(mmFrom, 10) == parseInt(mmTo, 10) && parseInt(yyFrom, 10) == parseInt(yyTo, 10))
    {
        return false;
    }
    return true;
}

function utils_isDate(dateStr)
{
    //date in format of dd-mm-yyyy

    var datePat = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;

    var matchArray = dateStr.match(datePat); // is the format ok?

    if (matchArray == null)
    {
        return false;
    }

    //matchArray = ["16-01-2003", "16", "-", "01", "-", "2003", index: 0, input: "16-01-2003"]

    var month = matchArray[3]; //parse date into variables
    var day = matchArray[1];
    var year = matchArray[5];

    if (month < 1 || month > 12)
    { // check month range
        return false;
    }

    if (day < 1 || day > 31)
    {
        return false;
    }

    if ((month == 4 || month == 6 || month == 9 || month == 11) && day == 31)
    {
        return false;
    }

    if (month == 2)
    { // check for february 29th
        var isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
        if (day > 29 || (day == 29 && !isleap))
        {
            return false;
        }
    }
    return true; // date is valid
}

function utils_isValidTime(timeStr)
{
    // Checks if time is in HH:MM:SS AM/PM format.
    // The seconds and AM/PM are optional.

    var timePat = /^(\d{1,2}):(\d{2})$/;

    var matchArray = timeStr.match(timePat);
    if (matchArray == null)
    {
        return false;
    }

    var hour = matchArray[1];
    var minute = matchArray[2];


    if (hour < 0 || hour > 23)
    {
        return false;
    }

    if (minute < 0 || minute > 59)
    {
        return false;
    }

    return true;
}

function utils_spclCharValidation(str) {
    return !/[~`!#()_£@$%\^&*+=\\[\]\\;,/{}|\\":<>\?]/g.test(str);
}

function utils_spclCharValidation_allowbackslash(str) {
    return !/[~`!#()_£@$%\^&*+=\\[\]\\;,{}|\\":<>\?]/g.test(str);
}


function utils_deepCopy(obj) {
    if (Object.prototype.toString.call(obj) === '[object Array]') {
        var out = [], i = 0, len = obj.length;
        for (; i < len; i++) {
            out[i] = arguments.callee(obj[i]);
        }
        return out;
    }
    if (typeof obj === 'object') {
        var out = {}, i;
        for (i in obj) {
            out[i] = arguments.callee(obj[i]);
        }
        return out;
    }
    return obj;
}

function utils_array_intersection(a, b)
{
    var result = new Array();
    while (a.length > 0 && b.length > 0)
    {
        if (a[0] < b[0]) {
            a.shift();
        } else if (a[0] > b[0]) {
            b.shift();
        } else /* they're equal */
        {
            result.push(a.shift());
            b.shift();
        }
    }

    return result;
}

function utils_isIdInCombo(cbo, id)
{
    //return true if id is in the combo list
    //false otherwise
    var opt = cbo.getOption(id);

    if (opt)
    {
        return true;
    } else
    {
        return false;
    }
}

function utils_stringStartsWith(string, prefix) {
    return string.slice(0, prefix.length) == prefix;
}

function utils_post_to_url(path, params, method, target)
{
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);
    form.setAttribute("target", target);
    for (var key in params) {
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", key);
        hiddenField.setAttribute("value", params[key]);
        form.appendChild(hiddenField);
    }

    document.body.appendChild(form);
    form.submit();
}

function utils_guid() {

    //generates a unique id

    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
    }
    return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
            s4() + '-' + s4() + s4() + s4();
}


function propertyFromStylesheet(selector, attribute) {
    var value;

    [].some.call(document.styleSheets, function (sheet) {
        return [].some.call(sheet.rules, function (rule) {
            if (selector === rule.selectorText) {
                return [].some.call(rule.style, function (style) {
                    if (attribute === style) {
                        value = rule.style.getPropertyValue(attribute);
                        return true;
                    }

                    return false;
                });
            }

            return false;
        });
    });

    return value;
}

function utils_getRandomArbitrary(min, max) {
    //returns a random number between min (inclusive) and max(exclusive)
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function utils_hasWhiteSpace(s) {
    //returns true if a string has a white space in it
    return s.indexOf(' ') >= 0;
}

function utils_validateEmail(emailadd)
{
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(emailadd);
}

function utils_validatePassword(str)
{
    /*
     8 characters length
     2 letters in Upper Case
     1 Special Character (!@#$&*)
     2 numerals (0-9)
     3 letters in Lower Case    
     */
    return /^(?=.*[A-Z].*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{8}$/.test(str);
}


function utils_response_connected(loader, title, errmsg)
{
    //returns true if was connected to server, false otherwise
    if (loader.xmlDoc.responseURL == "")
    {
        dhtmlx.alert({
            text: errmsg,
            type: "alert-warning",
            title: title,
            callback: function () {
            }
        });
        return false;
    }

    return true;
}

function utils_response_extract_jsonobj(loader, tfAlert, title, overideMsg)
{
    var jsonObj = null;
    var err_msg = "";

    //extract json loader
    try {
        jsonObj = JSON.parse(loader.xmlDoc.responseText);

    } catch (e) {
        err_msg = loader.xmlDoc.responseText;

        if (tfAlert)
        {
            if (overideMsg != "")
            {
                err_msg = overideMsg;
            }
            dhtmlx.alert({
                text: err_msg,
                type: "alert-warning",
                title: title,
                callback: function () {
                }
            });
        }
    }

    return jsonObj;
}

function utils_validate_autocompletecombo(cbo)
{
    var value = cbo.getSelectedValue();
    if (!value)
    {
        return false;
    }

    if (utils_trim(value, " ") == "")
    {
        return false;
    }

    return true;
}

function utils_dhxSerializeGridToJson(grid) {

    var arr_rows = [];
    grid.forEachRow(function (id) {

        var json_obj = {rwid: id, cells: {}};
        grid.forEachCell(id, function (cellObj, ind) {

            var cellid = grid.getColumnId(ind);
            var cellvalue = grid.cells(id, ind).getValue();

            json_obj.cells[cellid] = cellvalue;
        });

        arr_rows.push(json_obj);
    });


    return JSON.stringify(arr_rows);
}


function utils_validateGridOverlap(grid, id, colid_dtfrom, colid_dtto, dtfrom, dtto)
{
    if (utils_isDate(dtfrom) && utils_isDate(dtto))
    {
        for (var i = 0; i < grid.getRowsNum(); i++) {
            var rwid = grid.getRowId(i);
            if (rwid != id)
            {
                var rwdtfrom = grid.cells2(i, grid.getColIndexById(colid_dtfrom)).getValue();
                var rwdtto = grid.cells2(i, grid.getColIndexById(colid_dtto)).getValue();

                if (utils_isDate(rwdtfrom) && utils_isDate(rwdtto))
                {
                    //(dtfrom <= rwdtto AND dtto >= rwdtfrom)
                    var chk1 = utils_validateDateOrder(dtfrom, rwdtto);
                    var chk2 = utils_validateDateOrder(dtto, rwdtfrom);
                    if (chk1 && chk2)
                    {
                        return false;
                    }
                }
            }
        }
    }

    return true;
}


function utils_formatGridRows(grid, css)
{
    grid.forEachRow(function (id) { // function that gets id of the row as an incoming argument
        grid.setRowTextStyle(id, css);
    });

}


function utils_toTitleCase(str) {
    return str.replace(/\w\S*/g, function (txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}


function utils_containsObject(obj, list) {
    var i;
    for (i = 0; i < list.length; i++) {
        if (list[i] === obj) {
            return true;
        }
    }

    return false;
}

