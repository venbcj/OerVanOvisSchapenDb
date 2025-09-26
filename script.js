
const nav = document.querySelector('nav'),
      navOffset = nav.offsetTop;

      window.addEventListener('scroll', () => {
        if (window.pageYoffset >= navOffset) {
            nav.classList.add('sticky');
        } else {
            nav.classList.remove('sticky');
        }
      })

