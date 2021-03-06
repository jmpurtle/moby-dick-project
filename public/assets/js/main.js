var wordList = document.getElementsByTagName('vue-data')[0].innerHTML;

var vueInstance = new Vue({
	el: '#pageContainer',
	data() {
		return {
			show: true,
			items: JSON.parse(wordList)
		}
	},
	methods: {
		delayedBy: function(el) {
			return 100
		},
		beforeEnter: function(el) {
			el.style.opacity = 0;
		},
		enter: function(el, done) {
			let delay = el.dataset.index * this.delayedBy();
			setTimeout(function() {
				$(el).animate({ opacity: 1 }, 300, done);
			}, delay);
		},
		leave: function(el, done) {
			let delay = el.dataset.index * this.delayedBy();
			setTimeout(function() {
				$(el).animate({ opacity: 0 }, 300, done);
			}, delay);
		}
	}
});
