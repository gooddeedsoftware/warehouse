// calculate timedifference between two given times
function calculateTimeDifference(start_time, end_time, start_date, end_date) {
    start = start_time.split(":");
    end = end_time.split(":");
    var regExp = /(\d{1,2})\.(\d{1,2})\.(\d{2,4})/;
    if (parseInt(end_date.replace(regExp, "$3$2$1")) == parseInt(start_date.replace(regExp, "$3$2$1")) || end_date == "") {
        //startDate = (0,0,0);
        startDate = ["0", "0", "0"];
        endDate = ["0", "0", "0"];
    } else {
        startDate = start_date.split(".");
        endDate = end_date.split(".");
    }
    try {
        var startDate = new Date(startDate[2], startDate[1], startDate[0], start[0], start[1], 0);
        var endDate = new Date(endDate[2], endDate[1], endDate[0], end[0], end[1], 0);
        var diff = endDate.getTime() - startDate.getTime();
        var hours = Math.floor(diff / 1000 / 60 / 60);
        diff -= hours * 1000 * 60 * 60;
        var minutes = Math.floor(diff / 1000 / 60);
        var calculated_hours = '0.0';
        if (minutes > 1 && minutes < 15) {
            minutes = 15;
        } else if (minutes > 15 && minutes < 30) {
            minutes = 30;
        } else if (minutes > 30 && minutes < 45) {
            minutes = 45;
        } else if (minutes > 45) {
            hours = hours + 1;
        }
        switch (minutes) {
            case 15:
                minutes = 0.25;
                break;
            case 30:
                minutes = 0.5;
                break;
            case 45:
                minutes = 0.75;
                break;
            default:
                minutes = 0;
                break;
        }
        if (hours > 0) {
            //var calculated_hours = ( hours < 9 ? "0" : "") + hours + "." + (minutes > 0 ?  minutes :"0");
            var calculated_hours = hours + minutes;
        } else {
            var calculated_hours = (minutes > 0 ? minutes : "0");
        }
        calculated_hours = Number(calculated_hours).toFixed(2);
        return calculated_hours.replace(".", ",");
    } catch (Exception) {
        console.log("exception");
    }
}
// get current date
function getCurrentDate(format) {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    return dd + '.' + mm + '.' + yyyy;
}
// Accept only numbers and comma and minus
function checkNumberWithCommaAndMinus(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode(key);
    if (key.length == 0) return;
    var regex = /^[0-9.,\-\b]+$/;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
}
// Accept only numbers and minus
function checkNumberWithMinus(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode(key);
    if (key.length == 0) return;
    var regex = /^[0-9.\-\b]+$/;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
}
// Accept only numbers
function checkNumber(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode(key);
    if (key.length == 0) return;
    var regex = /^[0-9\b]+$/;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
}
// uuid
function createUUID() {
    var s = [];
    var hexDigits = "0123456789abcdefghijklmnopqrstuvwxyz";
    for (var i = 0; i < 32; i++) {
        s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
    }
    s[14] = "4"; // bits 12-15 of the time_hi_and_version field to 0010
    s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1); // bits 6-7 of the clock_seq_hi_and_reserved to 01
    s[8] = s[13] = s[18] = s[23];
    var uuid = s.join("");
    return uuid;
}
$(document).on("change", ".validateEmail", function() {
    if (!validateEmail($(this).val())) {
        alert(emailvalidationmsg);
        $(this).val("");
        $(this).focus();
    }
})

function validateEmail(email) {
    var mail = String(email);
    mail = mail.replace(/Æ/gi, 'a');
    mail = mail.replace(/Ø/gi, 'a');
    mail = mail.replace(/Å/gi, 'a');
    mail = mail.replace(/æ/gi, 'a');
    mail = mail.replace(/ø/gi, 'a');
    mail = mail.replace(/å/gi, 'a');
    console.log(mail);
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
        return (true)
    }
    return (false)
}
// Validate Numbers
$(document).on("keypress", ".validateNumbers", function(e) {
    validateNumbers(e);
});
/**
 * [validateNumbers description]
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function validateNumbers(obj) {
    var regex = new RegExp("^[0-9]+$");
    var str = String.fromCharCode(!obj.charCode ? obj.which : obj.charCode);
    if (regex.test(str)) {
        return true;
    } else {
        obj.preventDefault();
        return false;
    }
}
// Validate Numbers with comma
$(document).on("keypress", ".validateNumbersWithComma", function(e) {
    var regex = new RegExp("^[0-9,]+$");
    var str = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (regex.test(str)) {} else {
        event.preventDefault();
        return false;
    }
    //this.value = this.value.replace(/[^0-9\.]/g,'');
    $(this).val($(this).val().replace(/[^0-9\,]/g, ''));
    if ((event.which != 46 && event.which == 188 || $(this).val().indexOf('.') != -1 && (event.which < 48 || event.which > 57) || $(this).val().indexOf(',') != -1) && event.which != 188 && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
});
//Added by David - To allow only single minus and numbers
$(document).on('keypress keyup blur', '.numberWithSingleMinus', function(event) {
    var regex = new RegExp("^[0-9-]+$");
    var str = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (regex.test(str)) {} else {
        event.preventDefault();
        return false;
    }
    $(this).val($(this).val().replace(/[^0-9\-]/g, ''));
    if ((event.which != 46 && event.which == 188 || $(this).val().indexOf('-') != -1 && (event.which < 48 || event.which > 57) || $(this).val().indexOf(',') != -1) && event.which != 188 && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
        return false;
    }
});
//Added by david-To allow only one comma in price fields
$(document).on('keypress keyup blur', '.numberWithSingleComma', function(event) {
    var regex = new RegExp("^[0-9,]+$");
    var str = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (regex.test(str)) {} else {
        event.preventDefault();
        return false;
    }
    //this.value = this.value.replace(/[^0-9\.]/g,'');
    $(this).val($(this).val().replace(/[^0-9\,]/g, ''));
    if ((event.which != 46 && event.which == 188 || $(this).val().indexOf('.') != -1 && (event.which < 48 || event.which > 57) || $(this).val().indexOf(',') != -1) && event.which != 188 && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
});
$(document).on('keypress keyup blur', '.numberWithSingleMinusAndComma', function(event) {
    var regex = new RegExp("^[0-9,-]+$");
    var str = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (regex.test(str)) {} else {
        event.preventDefault();
        return false;
    }
});
/**
 * [replaceDot description]
 * @param  {[type]} value [description]
 * @return {[type]}       [description]
 */
function replaceDot(value) {
    if (value) {
        value = parseFloat(value);
        value = value.toFixed(2);
        value = value.toString();
        value = value.replace('.', ',');
        return value;
    } else {
        return '0,00';
    }
}
/**
 * [replaceComma description]
 * @return {[type]} [description]
 */
function replaceComma(value) {
    if (value) {
        value = value.toString();
        value = value.replace(',', '.');
        value = parseFloat(value);
        value = value.toFixed(2);
        return parseFloat(value);
    } else {
        return 0.00;
    }
}
/**
 * [replaceComma description]
 * @return {[type]} [description]
 */
function replaceCommaWithNoDecimal(value) {
    if (value) {
        value = value.toString();
        value = value.replace(',', '.');
        value = parseFloat(value);
        value = value.toFixed(0);
        return value;
    } else {
        return '';
    }
}


function showAlertMessage(message, type = 'warning') {
    new PNotify({
        title: message_text,
        text: message,
        type: type,
        delay: 3000,
    });
}