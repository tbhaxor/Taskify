$(document).ready(function () {
    const qp = new URLSearchParams(window.location.search);
    const errorMessage = qp.get("error");
    const infoMessage = qp.get("message");

    if (errorMessage) {
        $("#alert-container").after(
            `<div class="alert alert-danger" role="alert">${errorMessage}</div>`
        );
    }

    if (infoMessage) {
        $("#alert-container").after(
            `<div class="alert alert-info" role="alert">${infoMessage}</div>`
        );
    }
});
