/*
 * Tệp JavaScript tạo các tương tác cho trang cá nhân.
 * Chức năng 1: chuyển giao diện sáng/tối và lưu lựa chọn bằng localStorage.
 * Chức năng 2: thu gọn/mở rộng các mục thông tin bằng accordion.
 * Cách thử: dùng chuột hoặc phím Tab, Enter/Space để thao tác các nút.
 */

document.addEventListener("DOMContentLoaded", function () {
    // =========================
    // 1. NÚT CHUYỂN SÁNG / TỐI
    // =========================
    const darkModeContainer = document.createElement("div");
    darkModeContainer.id = "dark-mode-container";

    const darkModeButton = document.createElement("button");
    darkModeButton.type = "button";
    darkModeButton.id = "dark-mode-button";
    darkModeButton.textContent = "🌙 Chế độ tối";
    darkModeButton.setAttribute("aria-label", "Chuyển sang chế độ tối");

    darkModeContainer.appendChild(darkModeButton);
    document.body.prepend(darkModeContainer);

    const savedTheme = localStorage.getItem("theme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark-mode");
        darkModeButton.textContent = "☀️ Chế độ sáng";
        darkModeButton.setAttribute("aria-label", "Chuyển sang chế độ sáng");
    }

    darkModeButton.addEventListener("click", function () {
        const isDark = document.body.classList.toggle("dark-mode");

        if (isDark) {
            localStorage.setItem("theme", "dark");
            darkModeButton.textContent = "☀️ Chế độ sáng";
            darkModeButton.setAttribute(
                "aria-label",
                "Chuyển sang chế độ sáng"
            );
        } else {
            localStorage.setItem("theme", "light");
            darkModeButton.textContent = "🌙 Chế độ tối";
            darkModeButton.setAttribute(
                "aria-label",
                "Chuyển sang chế độ tối"
            );
        }
    });

    // =========================
    // 2. ACCORDION
    // =========================
    const boxes = document.querySelectorAll(".info-box, .full-box");

    boxes.forEach(function (box) {
        const heading = box.querySelector("h2");

        if (!heading) {
            return;
        }

        if (heading.textContent.includes("Thời khóa biểu")) {
            return;
        }

        const accordionButton = document.createElement("button");

        accordionButton.type = "button";
        accordionButton.classList.add("accordion-button");
        accordionButton.textContent = "Thu gọn ▲";
        accordionButton.setAttribute("aria-expanded", "true");

        heading.insertAdjacentElement("afterend", accordionButton);

        accordionButton.addEventListener("click", function () {
            const isCollapsed = box.classList.toggle("is-collapsed");

            accordionButton.textContent = isCollapsed
                ? "Mở rộng ▼"
                : "Thu gọn ▲";

            accordionButton.setAttribute(
                "aria-expanded",
                String(!isCollapsed)
            );
        });
    });
});