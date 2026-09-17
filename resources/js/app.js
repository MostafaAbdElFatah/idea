import Alpine from 'alpinejs';

import './password';

window.Alpine = Alpine;
window.ideaForm = ({ links = [], steps = [] } = {}) => ({
	links: [...links],
	hasImage: false,
	imagePreview: null,
	previewImage(event) {
		const [file] = event.target.files;

		this.revokeImagePreview();
		this.hasImage = Boolean(file);
		this.imagePreview = file ? URL.createObjectURL(file) : null;
	},
	clearImage() {
		this.revokeImagePreview();
		this.$refs.image.value = '';
		this.hasImage = false;
		this.imagePreview = null;
	},
	revokeImagePreview() {
		if (this.imagePreview) {
			URL.revokeObjectURL(this.imagePreview);
		}
	},
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
window.masonryGrid = () => ({
	rowGap: 24,
	init() {
		const observer = new ResizeObserver(() => this.layout());

		[...this.$el.children].forEach((item) => observer.observe(item));
	},
	layout() {
		[...this.$el.children].forEach((item) => {
			item.style.gridRowEnd = `span ${Math.ceil(item.getBoundingClientRect().height) + this.rowGap}`;
		});
	},
});
window.avatarPicker = (currentUrl = null) => ({
	currentUrl,
	preview: currentUrl,
	selected: false,
	confirmRemove: false,
	pick(event) {
		const [file] = event.target.files;

		this.revoke();

		if (!file) {
			this.clear();
			return;
		}

		this.selected = true;
		this.preview = URL.createObjectURL(file);
		this.$refs.input.form.enctype = 'multipart/form-data';
	},
	clear() {
		this.revoke();
		this.$refs.input.value = '';
		this.selected = false;
		this.preview = this.currentUrl;
		this.$refs.input.form.enctype = 'application/x-www-form-urlencoded';
	},
	revoke() {
		if (this.selected && this.preview) {
			URL.revokeObjectURL(this.preview);
		}
	},
});

Alpine.start();
