window.addEventListener("load", function () {
  const loader = document.getElementById('loading-screen');
  const pageContent = document.getElementById('page-content');

  if (loader) {
    setTimeout(() => {
      loader.style.display = 'none';
      if (pageContent) pageContent.style.display = 'block';
    }, 3000);
  } else {
    if (pageContent) pageContent.style.display = 'block';
  }
});
