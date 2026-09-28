/*
 * canhan.js - Trang cá nhân Lê Nguyễn Gia Nhân.
 * Tương tác 1: chuyển giao diện sáng/tối và lưu bằng localStorage.
 * Tương tác 2: tìm kiếm/lọc danh sách kỹ năng theo từ khóa.
 * Cách thử: bấm nút đổi giao diện và nhập HTML, CSS, Git... vào ô tìm kiếm.
 */

document.addEventListener("DOMContentLoaded", function () {

    /* ==================================================
       1. CHUYỂN GIAO DIỆN SÁNG / TỐI
       ================================================== */

    const themeButton = document.getElementById("theme-toggle");

    const savedTheme = localStorage.getItem("nhan-theme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark-theme");

        themeButton.textContent = "☀️ Chế độ sáng";
        themeButton.setAttribute("aria-pressed", "true");
    }


    themeButton.addEventListener("click", function () {

        document.body.classList.toggle("dark-theme");

        const darkMode =
            document.body.classList.contains("dark-theme");

        if (darkMode) {

            themeButton.textContent = "☀️ Chế độ sáng";
            themeButton.setAttribute("aria-pressed", "true");

            localStorage.setItem("nhan-theme", "dark");

        } else {

            themeButton.textContent = "🌙 Chế độ tối";
            themeButton.setAttribute("aria-pressed", "false");

            localStorage.setItem("nhan-theme", "light");
        }

    });


    /* ==================================================
       2. TÌM KIẾM / LỌC KỸ NĂNG
       ================================================== */

    const skillInput =
        document.getElementById("skill-filter");

    const skillList =
        document.getElementById("skill-list");

    const skillItems =
        skillList.querySelectorAll("li");

    const skillMessage =
        document.getElementById("skill-message");


    skillInput.addEventListener("input", function () {

        const keyword =
            skillInput.value
                .trim()
                .toLowerCase();

        let visibleCount = 0;


        skillItems.forEach(function (item) {

            const skillName =
                item.textContent.toLowerCase();

            const matched =
                skillName.includes(keyword);

            item.classList.toggle(
                "skill-hidden",
                !matched
            );

            if (matched) {
                visibleCount++;
            }

        });


        if (keyword === "") {

            skillMessage.textContent = "";

        } else if (visibleCount === 0) {

            skillMessage.textContent =
                "Không tìm thấy kỹ năng phù hợp.";

        } else {

            skillMessage.textContent =
                "Tìm thấy " +
                visibleCount +
                " kỹ năng phù hợp.";
        }

    });

});