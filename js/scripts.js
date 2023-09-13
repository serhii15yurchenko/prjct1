AOS.init();

$('input[name=phone]').mask("+38 (999) 999 99 99");

const BURGER = document.querySelector('.burger');
const MOBILE_MENU = document.querySelector('.mobile-menu');
const BG_MOBILE = document.querySelector('.bg-mobile');
const MOB_LINK = document.querySelectorAll('.mobile-link');

BURGER.addEventListener('click', function() {
    BURGER.classList.toggle('burger-open');
    MOBILE_MENU.classList.toggle('mobile-menu-open');
    BG_MOBILE.classList.toggle('bg-mobile-open');
    document.body.classList.toggle('no-scroll');
})

BG_MOBILE.addEventListener('click', function() {
    BURGER.classList.remove('burger-open');
    MOBILE_MENU.classList.remove('mobile-menu-open');
    BG_MOBILE.classList.remove('bg-mobile-open');
    document.body.classList.remove('no-scroll');
});

for (let i = 0; i < MOB_LINK.length; i++) {
    MOB_LINK[i].addEventListener('click', function() {
        BURGER.classList.remove('burger-open');
        MOBILE_MENU.classList.remove('mobile-menu-open');
        BG_MOBILE.classList.remove('bg-mobile-open');
        document.body.classList.remove('no-scroll');

    });
}

$(document).ready(function() {
    var slider = $('.reviews-list');
    var videoElements = document.querySelectorAll('.player');
    var players = [];
  
    // Ініціалізація Plyr для кожного відео
    videoElements.forEach(function(element) {
      var player = new Plyr(element, {
        // autoplay: true, 
        loop: { active: true } 
      });
      players.push(player);
    });
  
    // Ініціалізація слайдера Slick
    slider.slick({
      slidesToShow: 1,
      slidesToScroll: 1,
    //   autoplay: true,
    //   autoplaySpeed: 2000,
      dots: true,
      arrows: true,
      draggable: false,
    //   fade: true,
        adaptiveHeight: true,
        infinite: false,
    });
  
    // Обробник події перед зміною слайда
    slider.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
      // Призупиняємо відео на поточному слайді
      players[currentSlide].pause();
    });
});

const ITEM_TOP = document.querySelectorAll('.accordion-top');

ITEM_TOP.forEach((element) => {
    element.addEventListener('click', () => {
        element.classList.toggle('accordion-top-active');
        let itemBottom = element.nextElementSibling;
        if(itemBottom.style.maxHeight) {
            itemBottom.style.maxHeight = null;
        } else {
            itemBottom.style.maxHeight = itemBottom.scrollHeight + "px";
        }
    });
});

const tariffBtn = document.querySelectorAll('.tariff-accrordion-btn');

tariffBtn.forEach((element) => {
    element.addEventListener('click', () => {
        element.classList.toggle('tariff-accrordion-btn-open');
        let tariffWrap = element.previousElementSibling;
        // tariffWrap.style.display = 'block';
        // tariffWrap.classList.toggle('tariff-accordion-wrap-open');

        if(tariffWrap.style.maxHeight) {
            tariffWrap.style.maxHeight = null;
        } else {
            tariffWrap.style.maxHeight = tariffWrap.scrollHeight + "px";
            
        }
        $(".tariff-list").slick("refresh");
    });
});

const TABS = document.querySelectorAll('.tab');
const TABS_CONTENT = document.querySelectorAll('.tab-panel');

TABS.forEach((tab) => {
    tab.addEventListener('click', () => {
        const target = document.querySelector(tab.dataset.target);

        TABS_CONTENT.forEach((tc) => {
            tc.classList.remove('tab-panel-active');
        })

        target.classList.add('tab-panel-active');

        TABS.forEach((t) => {
            t.classList.remove('tab-active');
        })

        tab.classList.add('tab-active');
    })
});


$('.salary-slider').slick({
    slidesToShow: 2,
    slidesToScroll: 1,
    rows: 2,
    swipeToSlide: true,
    variableWidth: true,
    dots: true,
});

$('.tariff-list').slick({
    infinite: false,
    // infinite: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    adaptiveHeight: true,
    responsive: [
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
            adaptiveHeight: true,

          },
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            adaptiveHeight: true,
          },
        },
        // {
        //   breakpoint: 800,
        //   settings: 'unslick',
        // },
      ],
});



$(function() {
    $('form').submit(function(e) {
      var $form = $(this);
      $.ajax({
        type: 'POST',
        url: 'order.php',
        data: $form.serialize()
      }).done(function() {
        if (typeof fbq === 'function') {
            fbq('track', 'Lead');
        }
        console.log('success');
        Swal.fire({
            position: 'top',
            icon: 'success',
            title: 'Дякуємо!',
            text: 'Наш менеджер скоро зв`яжеться з Вами!',
            showConfirmButton: false,
            timer: 2500
        })
      }).fail(function() {
        console.log('fail');
        Swal.fire({
            position: 'top',
            icon: 'error',
            title: 'Помилка',
            text: 'Будь ласка, заповніть форму ще раз!',
        })
      });
      //отмена действия по умолчанию для кнопки submit
      e.preventDefault(); 
    });
});

// block button in form on event submit
let forms = document.querySelectorAll('form');
let translates = {
    uk: 'Відправка...',
    ru: 'Отправка...',
    en: 'Sending...',
}
let lang = document.documentElement.lang;

if (lang.includes('ru')) {
    str = translates.ru;
} else if (lang.includes('uk')) {
    str = translates.uk;
} else {
    str = translates.en;
}

forms.forEach((form) => {

    form.addEventListener('submit', function () {

        let btn = form.querySelector('button');
        btn.textContent = str;
        btn.style.backgroundColor = 'gray';
        btn.setAttribute('disabled', true);
        console.log('button in form - blocked')
    })
})
// block button in form on event submit