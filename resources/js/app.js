import "./bootstrap";

document.addEventListener("alpine:init", () => {
    Alpine.store("auth", {
        user() {
            return JSON.parse(localStorage.getItem("user")) || null;
        },
        token() {
            return localStorage.getItem("token") || null;
        },
        set(user, token) {
            localStorage.setItem("user", JSON.stringify(user));
            localStorage.setItem("token", token);
        },
    });
    Alpine.store("call", {
        async callJson(
            method = "POST",
            url,
            queryData = null,
            postData = null,
            redirectToLogin = false,
        ) {
            let finalUrl = url;

            if (queryData) {
                const params = new URLSearchParams(queryData);
                const queryString = params.toString();
                if (queryString) {
                    finalUrl = url + "?" + queryString;
                }
            }

            const headers = {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            };
            const token = Alpine.store("auth").token();
            if (token) {
                headers["Authorization"] = "Bearer " + token;
            }

            const response = await fetch(finalUrl, {
                method,
                headers,
                body: postData === null ? null : JSON.stringify(postData),
            });

            if (response.status === 401 && redirectToLogin) {
                window.location.href = "/auth/login";
            } else {
                return response;
            }
        },
    });
    Alpine.store("alert", {
        error(message, errors = {}) {
            const items = Object.entries(errors || {}).flatMap(
                ([field, messages]) => {
                    return (messages || []).map((msg) => `${field}: ${msg}`);
                },
            );
            const html = items.length
                ? `<ul class="text-start m-0"> ${items.map((i) => `<li>${i}</li>`).join("")} </ul>`
                : null;
            Swal.fire({
                text: message,
                icon: "error",
                showCloseButton: true,
                showConfirmButton: false,
                timerProgressBar: true,
                html: html,
                position: "center",
            });
        },
        success(text) {
            Swal.fire({
                text: text,
                icon: "success",
                timer: 1500,
                showCloseButton: true,
                showConfirmButton: false,
                timerProgressBar: true,
                toast: true,
                position: "bottom",
            });
        },
        confirm(text, func) {
            that = this;
            Swal.fire({
                text: text,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "red",
                confirmButtonText: that.trans.Yes,
                cancelButtonText: that.trans.No,
                position: "center",
            }).then((result) => func(result));
        },
    });
});

Alpine.start();
