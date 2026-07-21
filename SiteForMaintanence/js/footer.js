function loadFooter() {
    fetch('footer.html')
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.text();
      })
      .then(data => {
        const footerContainer = document.querySelector('.page-wrapper');
        footerContainer.insertAdjacentHTML('beforeend', data);
      })
      .catch(error => console.error('Error loading footer:', error));
  }
  
  document.addEventListener('DOMContentLoaded', loadFooter);