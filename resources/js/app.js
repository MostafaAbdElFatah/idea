import Alpine from 'alpinejs';

import './password';

window.Alpine = Alpine;
window.ideaForm = ({ links = [], steps = [] } = {}) => ({
	links: [...links],
	steps: [...steps],
	url: '',
	step: '',
	urlError: '',
	stepError: '',
	addLink() {
		const url = this.url.trim();

		if (!url) {
			this.urlError = 'Please enter a URL.';
			return;
		}

		if (!this.$refs.url.checkValidity()) {
			this.urlError = 'Please enter a valid URL.';
			return;
		}

		if (this.links.includes(url)) {
			this.urlError = 'This URL has already been added.';
			return;
		}

		this.links.push(url);
		this.url = '';
		this.urlError = '';
	},
	removeLink(index) {
		this.links.splice(index, 1);
	},
	addStep() {
		const value = this.step.trim();

		if (!value) {
			this.stepError = 'Please enter a step.';
			return;
		}

		if (this.steps.includes(value)) {
			this.stepError = 'This step has already been added.';
			return;
		}

		this.steps.push(value);
		this.step = '';
		this.stepError = '';
	},
	removeStep(index) {
		this.steps.splice(index, 1);
	},
});

Alpine.start();
