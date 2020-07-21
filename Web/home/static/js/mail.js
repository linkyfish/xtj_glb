$(document).ready(function () {
	var MailData = ["gmail.com", "yahoo.com.tw", "hotmail.com", "qq.com", "163.com", "yahoo.com.hk"];
	$("input[type=email]").autocomplete({
		autoFocus: true,
		source: function (request, response) {
			var KeyValue = request.term,
				index = KeyValue.indexOf("@"),
				Address = KeyValue,
				host = "",
				result = [];

			result.push(KeyValue);
			if (index > -1) {
				Address = KeyValue.slice(0, index);
				host = KeyValue.slice(index + 1);
			}
			if (Address) {
				var findedHosts = (host ? $.grep(MailData, function (value) {
					return value.indexOf(host) > -1;
				}) : MailData),
				findedResults = $.map(findedHosts, function (value) {
					return Address + "@" + value;
				});
				result = result.concat($.makeArray(findedResults));
			}
			response(result);
		}
	});
});
