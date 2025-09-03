setTimeout(function () {
  const loader = document.getElementById('loading-screen');
  const homepage = document.getElementById('homepage');
  const main = document.querySelector('main');

  if (loader) loader.style.display = 'none';
  if (homepage) {
    homepage.style.display = 'block';
  }
  if (main) {
    main.style.display = 'block';
  }
}, 3000);
