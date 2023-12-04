class ProxyFormService {
    error = false
    xhr = null

    constructor(id) {
        this.el = this.getEl('#' + id);
        this.button = this.getButton();

        if (this.error) {
            return false;
        }

        this.initSubmitFunction();
    }

    getButton() {
        let button = this.el.find('button[type="submit"]')

        if (button.length === 0) {
            this.error = true;
            console.log("Элемент формы button не найден");
        }

        return button;
    }

    getEl(id) {
        let el = $(id);
        if (el.length === 0) {
            this.error = true;
            console.log("Родительский элемент form не найден");
        }

        return el;
    }

    initSubmitFunction() {
        let self = this;

        this.el.on('submit', function (e) {
            e.preventDefault();
            e.stopPropagation();

            let formData = new FormData(self.el[0]),
                jsonData = JSON.stringify(Object.fromEntries(formData));

            if (!jsonData || self.xhr !== null) {
                return false;
            }

            self.beforeXHR();
            self.xhr = $.ajax({
                url: '/task',
                data: jsonData,
                type: 'POST',
                dataType: 'json'
            }).done(function( data ) {
                window.location.href = "/task/" + data.id;

                self.afterXHR();
            }).fail(function($xhr) {
                let data = $.parseJSON($xhr.responseText);
                if ('errors' in data) {
                    let mes = '';
                    for (var key in data.errors) {
                        if (mes) {
                            mes += '; ';
                        }
                        mes += data.errors[key].message;
                    }

                    self.writeResultMessage(mes);
                } else {
                    self.writeResultMessage('Во время постановки задания в очередь произошла внутреняя ошибка попробуйте позже');
                }
                self.afterXHR();
            });
        });
    }

    writeResultMessage(message) {
        this.el.prepend("<div class='alert alert-danger' role='alert'>" + message + "</div>");
    }

    beforeXHR() {
        this.el.find('.alert').remove();
        this.button.html('<div class="spinner-grow spinner-grow-sm" role="status"><span class="visually-hidden">Loading...</span></div>');
    }

    afterXHR() {
        this.button.html('Отправить');
        this.xhr = null;
    }
}

$(function() {
    new ProxyFormService('proxyForm');
});