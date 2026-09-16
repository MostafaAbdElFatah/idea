import Alpine from 'alpinejs';

import './password';

window.Alpine = Alpine;
window.ideaLinks = () => ({
	links: [],
	url: '',
	urlError: '',
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
});

Alpine.start();
