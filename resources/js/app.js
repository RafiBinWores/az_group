import "./bootstrap";

// Theme save in local storage
document.addEventListener("DOMContentLoaded", function () {
    let menuBtn = document.querySelector(".button-menu-mobile");
    if (menuBtn) {
        menuBtn.addEventListener("click", function (e) {
            e.preventDefault();
            document.body.classList.toggle("sidebar-enable");
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    let closeBtn = document.querySelector(".right-bar .right-bar-toggle");
    if (closeBtn) {
        closeBtn.addEventListener("click", function (e) {
            e.preventDefault();
            document.body.classList.remove("right-bar-enabled");
        });
    }
});

// Password show/hide toggle functionality
document
    .getElementById("togglePassword")
    .addEventListener("click", function () {
        const passwordInput = document.getElementById("password");
        const icon = document.getElementById("togglePasswordIcon");
        const isPassword = passwordInput.type === "password";
        passwordInput.type = isPassword ? "text" : "password";
        icon.className = isPassword
            ? "fa-regular fa-eye-slash text-muted"
            : "fa-regular fa-eye text-muted";
    });

document
    .getElementById("togglePasswordConfirm")
    .addEventListener("click", function () {
        const passwordInput = document.getElementById("password_confirmation");
        const icon = document.getElementById("togglePasswordConfirmIcon");
        const isPassword = passwordInput.type === "password";
        passwordInput.type = isPassword ? "text" : "password";
        icon.className = isPassword
            ? "fa-regular fa-eye-slash text-muted"
            : "fa-regular fa-eye text-muted";
    });

// Show preview when user selects an image
const avatarInput = document.getElementById("avatar-input");
const avatarPreviewImg = document.getElementById("avatarPreviewImg");
const avatarDefaultSvg = document.getElementById("avatarDefaultSvg");
const deletePhotoBtn = document.getElementById("delete-photo-btn");

if (avatarInput) {
    avatarInput.addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                avatarPreviewImg.src = e.target.result;
                avatarPreviewImg.style.display = "block";
                avatarDefaultSvg.style.display = "none";
            };
            reader.readAsDataURL(file);
        } else {
            avatarPreviewImg.style.display = "none";
            avatarDefaultSvg.style.display = "block";
        }
    });
}

// Delete preview (and clear input)
if (deletePhotoBtn) {
    deletePhotoBtn.addEventListener("click", function () {
        avatarInput.value = "";
        avatarPreviewImg.src = "";
        avatarPreviewImg.style.display = "none";
        avatarDefaultSvg.style.display = "block";
    });
}
