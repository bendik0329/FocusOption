$(document).ready(function () {
    $("#SignInForm").on("submit", function (e) {
        e.preventDefault();

        username = $("#username").val();
        password = $("#password").val();
        lang = $("#selectLanguage").val();

        if (!lang) {
            lang = "ENG";
        }

        $("#SignInForm #errorMessage")
                .html("")
                .hide();
        $("#SignInBtn>div").addClass("loader disabled");
        $("#SignInBtn").prop("disabled", true);

        $.post("/index.php?act=login", {
            username: username,
            password: password,
            lang: lang,
            ajax: true
        })
                .done(function (json) {
                    var data = jQuery.parseJSON(json);

                    if (data.result == "error") {
                        $("#SignInForm #errorMessage")
                                .html(data.message)
                                .show();
                    }

                    if (data.result == "redirect") {
                        window.location.href = data.message;
                    }

                    if (data.result == "success") {
                        window.location.href = "/affiliate";
                    }
                })
                .always(function () {
                    $("#SignInBtn>div").removeClass("loader disabled");
                    $("#SignInBtn").prop("disabled", false);
                });
    });

    $("#ForgotForm").on("submit", function (e) {
        e.preventDefault();

        username = $("#forgotname").val();
        mail = $("#forgotemail").val();

        $("#ForgotForm #errorMessage")
                .html("")
                .hide();
        $("#ForgotBtnForm>div").addClass("loader disabled");
        $("#ForgotBtnForm").prop("disabled", true);

        $.post("./index.php?act=send_password", {
            username: username,
            mail: mail,
            ajax: true
        })
                .done(function (json) {
                    var data = jQuery.parseJSON(json);

                    if (data.result == "error") {
                        $("#ForgotForm #errorMessage")
                                .html(data.message)
                                .show();
                    }

                    if (data.result == "success") {
                        $("#ForgotForm").hide();
                        $("#ForgotFormComplete").show();
                    }
                })
                .always(function () {
                    $("#ForgotBtnForm>div").removeClass("loader disabled");
                    $("#ForgotBtnForm").prop("disabled", false);
                });
    });

    $("#SignInFormAdmin").on("submit", function (e) {
        e.preventDefault();

        username = $("#username").val();
        password = $("#password").val();

        $("#SignInFormAdmin #errorMessage")
                .html("")
                .hide();
        $("#SignInBtnAdmin>div").addClass("loader disabled");
        $("#SignInBtnAdmin").prop("disabled", true);

        $.post("./index.php?act=login", {
            username: username,
            password: password,
            ajax: true
        })
                .done(function (json) {
                    var data = jQuery.parseJSON(json);

                    if (data.result == "error") {
                        $("#SignInFormAdmin #errorMessage")
                                .html(data.message)
                                .show();
                    }

                    if (data.result == "success") {
                        document.location.reload();
                    }
                })
                .always(function () {
                    $("#SignInBtnAdmin>div").removeClass("loader disabled");
                    $("#SignInBtnAdmin").prop("disabled", false);
                });
    });

    $("#SignInFormManager").on("submit", function (e) {
        e.preventDefault();

        username = $("#username").val();
        password = $("#password").val();

        $("#SignInFormManager #errorMessage")
                .html("")
                .hide();
        $("#SignInBtnManager>div").addClass("loader disabled");
        $("#SignInBtnManager").prop("disabled", true);

        $.post("./index.php?act=login", {
            username: username,
            password: password,
            ajax: true
        })
                .done(function (json) {
                    var data = jQuery.parseJSON(json);

                    if (data.result == "error") {
                        $("#SignInFormManager #errorMessage")
                                .html(data.message)
                                .show();
                    }

                    if (data.result == "success") {
                        document.location = "/manager/?act=main";
                    }
                })
                .always(function () {
                    $("#SignInBtnManager>div").removeClass("loader disabled");
                    $("#SignInBtnManager").prop("disabled", false);
                });
    });

    $("#RegisterForm").on("submit", function (e) {
        e.preventDefault();

        $("#RegisterForm #errorMessage")
                .html("")
                .hide();
        $("#RegisterForm .invalid-feedback").remove();
        $("#RegisterForm input,#RegisterForm select").removeClass("is-invalid");
        $("#btnRegister>div").addClass("loader disabled");
        $("#btnRegister").prop("disabled", true);

        var formData = $("#RegisterForm").serialize() + "&ajax=true";

        $.post("/index.php?act=new_account", formData)
                .done(function (json) {
                    var data = jQuery.parseJSON(json);

                    console.log(data);

                    if (data.result === "error") {
                        $.each(data.errors, function (index, value) {
                            $(
                                    "#RegisterForm input[name=" +
                                    index +
                                    '], #RegisterForm select[name="db[' +
                                    index +
                                    ']"], #RegisterForm input[name="db[' +
                                    index +
                                    ']"]'
                                    )
                                    .addClass("is-invalid")
                                    .not("input[type=checkbox]")
                                    .after('<div class="invalid-feedback">' + value + "</div>");
                        });
                        $("#RegisterForm #errorMessage")
                                .html(data.message)
                                .show();
                    }

                    if (data.result === "success") {
                        window.location.href = data.message;
                    }

                    if (data.result === "pending") {
                        $("#RegisterForm").hide();
                        $("#RegisterFormComplete").show();
                    }
                })
                .always(function () {
                    $("#btnRegister>div").removeClass("loader disabled");
                    $("#btnRegister").prop("disabled", false);
                });
    });

    $("#selectLanguage").on("change", function () {
        window.location.href = "?lang=" + this.value;
    });

    $("#toRegisterPage, #toRegisterPage2").on("click", function (e) {
        console.log('111');
        e.preventDefault();
        $("#SignInForm").hide();
        $("#ForgotForm").hide();
        $("#RegisterForm").show();
    });

    $("#toSignInPage").on("click", function (e) {
        e.preventDefault();
        $("#RegisterForm").hide();
        $("#ForgotForm").hide();
        $("#SignInForm").show();
    });

    $("#toForgotPage, #toForgotPage2").on("click", function (e) {
        e.preventDefault();
        $("#RegisterForm").hide();
        $("#SignInForm").hide();
        $("#ForgotForm").show();
    });
});
