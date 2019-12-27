function checkReqFields(arrayFields) {

    var flag = true;

    arrayFields.each(function(i,elem) {
        if ($(elem).val() === '') {
            flag = false
        }
    });

    return flag;
}
