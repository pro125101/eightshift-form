import { Utils } from "../assets/utilities";

export class Migration {
	constructor(options) {
		/** @type Utils */
		this.utils = options.utils ?? new Utils();

		this.selector = options.selector;

		this.migrationRestUrl = options.migrationRestUrl;
	}

	init() {
		const elements = document.querySelectorAll(this.selector);

		[...elements].forEach((element) => {
			element.addEventListener('click', this.onClick, true);
		});
	}

	// Handle form submit and all logic.
	onClick = (event) => {
		event.preventDefault();

		const element = event.target;

		const formData = new FormData();

		formData.append('type', element.getAttribute('data-type'));

		// Populate body data.
		const body = {
			method: 'POST',
			mode: 'same-origin',
			headers: {
				Accept: 'application/json',
			},
			body: formData,
			credentials: 'same-origin',
			redirect: 'follow',
			referrer: 'no-referrer',
		};

		fetch(this.migrationRestUrl, body)
			.then((response) => {
				return response.json();
			})
			.then((response) => {
				const formElement = element.closest(this.utils.formSelector);

				this.utils.setGlobalMsg(formElement, response.message, response.status);

				setTimeout(() => {
					this.utils.hideGlobalMsg(formElement);
				}, 6000);
			});
	};
}