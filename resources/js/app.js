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
const passwordInput = document.getElementById("password");
const togglePasswordButton = document.getElementById("togglePassword");
const togglePasswordIcon = document.getElementById("togglePasswordIcon");

togglePasswordButton.addEventListener("click", () => {
    const isPassword = passwordInput.getAttribute("type") === "password";
    passwordInput.setAttribute("type", isPassword ? "text" : "password");
    togglePasswordIcon.className = isPassword
        ? "fa-regular fa-eye-slash"
        : "fa-regular fa-eye";
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