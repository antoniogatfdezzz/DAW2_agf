(() => {
	const $ = (sel) => document.querySelector(sel);
	const input = $("#input-text");
	const btn = $("#btn-concat");
	const p = $("#output");

	const concatToParagraph = () => {
		p.textContent += input.value;
		input.focus();
	};

	btn.addEventListener("click", concatToParagraph);

	input.addEventListener("keydown", (e) => {
		if (e.key === "Enter") {
			concatToParagraph();
		}
	});
})();
