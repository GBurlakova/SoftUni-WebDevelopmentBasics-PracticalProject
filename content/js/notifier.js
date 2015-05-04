var notifier = (function Notifier() {
    var MESSAGE_TIMEOUT = 3000;

    function Notifier(){
    }

    Notifier.prototype.showMessage = function (message, type) {
        switch (type){
            case 'info': showInfoMessage(message);
                break;
            case 'success': showSuccessMessage(message);
                break;

            case 'error': showErrorMessage(message);
                break;
            case 'confirm': showConfirmMessage(message);
                break;
        }
    };

    var showErrorMessage = function (msg) {
        var deferred = Q.defer();

        noty({
                text: msg,
                type: 'error',
                layout: 'topCenter',
                timeout: MESSAGE_TIMEOUT}
        );

        setTimeout(function () {
            deferred.resolve();
        }, MESSAGE_TIMEOUT);

        return deferred.promise;
    };

    var showSuccessMessage = function (msg) {
        var deferred = Q.defer();

        noty({
                text: msg,
                type: 'success',
                layout: 'topCenter',
                timeout: MESSAGE_TIMEOUT}
        );

        setTimeout(function () {
            deferred.resolve();
        }, MESSAGE_TIMEOUT);

        return deferred.promise;
    };

    var showInfoMessage = function (msg) {
        var deferred = Q.defer();

        noty({
                text: msg,
                type: 'info',
                layout: 'topCenter',
                timeout: MESSAGE_TIMEOUT}
        );

        setTimeout(function () {
            deferred.resolve();
        }, MESSAGE_TIMEOUT);

        return deferred.promise;
    };

    var showConfirmMessage = function (confirmMessage) {
        var deferred = Q.defer();

        noty(
            {
                text: confirmMessage,
                type: 'confirm',
                layout: 'topCenter',
                buttons: [
                    {
                        text : "Yes",
                        onClick : function($noty) {
                            deferred.resolve();
                            $noty.close();
                        }
                    },
                    {
                        text : "Cancel",
                        onClick : function($noty) {
                            deferred.reject();
                            $noty.close();
                        }
                    }
                ]}
        );

        return deferred.promise;
    };

    return Notifier;
})();