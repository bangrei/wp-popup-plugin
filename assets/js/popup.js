document.addEventListener("DOMContentLoaded", function () {
	const app = new Vue({
		el: "#popup-app",
		data: {
			showPopup: false,
			popupContent: {
				title: "",
				description: "",
			},
		},
		created() {
			const slugs = window.location.pathname.split("/");
			const slug = slugs.filter((it) => it != "").pop();
			// Call the API to get popup data
			fetch(`/wp-json/artistudio/v1/popup/${slug}`, {
				method: "GET",
				credentials: "same-origin",
			})
				.then((response) => response.json())
				.then((data) => {
					if (data.title && data.description) {
						this.popupContent = data;
						this.showPopup = true;
					}
				})
				.catch((error) => {
					console.error("Error fetching pop-up data: ", error);
				});
		},
		template: `
      <div v-if="showPopup" class="wp-popup">
        <div class="wp-popup-content">
          <span class="wp-popup-close" @click="showPopup = false">&times;</span>
          <h2>{{ popupContent.title }}</h2>
          <p>{{ popupContent.description }}</p>
        </div>
      </div>`,
	});
});
