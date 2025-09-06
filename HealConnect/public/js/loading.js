window.addEventListener("load", function () {
  const loader = document.getElementById('loading-screen');
  const pageContent = document.getElementById('page-content'); 

  if (document.body.classList.contains('homepage')) {
    setTimeout(() => {
      if (loader) loader.style.display = 'none';
      if (pageContent) pageContent.style.display = 'block';
    }, 3000);
  } else if (document.body.classList.contains('admin-login')) {
    setTimeout(() => {
      if (loader) loader.style.display = 'none';
      if (pageContent) pageContent.style.display = 'block';
    }, 1500);
  } else {
    if (loader) loader.style.display = 'none';
    if (pageContent) pageContent.style.display = 'block';
  }
});
