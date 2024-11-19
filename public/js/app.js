document.addEventListener("DOMContentLoaded", function () {
  const copyButtons = document.querySelectorAll(".copy-button");

  copyButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const pre = this.parentElement.querySelector("pre");
      const text = pre.textContent;

      navigator.clipboard.writeText(text).then(() => {
        const originalText = this.textContent;
        this.textContent = "Copied!";
        setTimeout(() => {
          this.textContent = originalText;
        }, 2000);
      });
    });
  });
});
