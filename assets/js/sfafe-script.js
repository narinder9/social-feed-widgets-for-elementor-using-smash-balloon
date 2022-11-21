class Sfafe_custom extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                wraper: '.sfafe_wraper.sfafe-common.carousel ',
                data: '.sfafe_wraper.sfafe-common.carousel .swiper-wrapper ',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $wraper: this.$element.find(selectors.wraper),
            $data: this.$element.find(selectors.data),    
        };
    }

    bindEvents() {
        var selector = this.elements.$wraper,
            autply = (this.elements.$data.attr('data-autoplay') == "yes") ? true : false,           
            speeds = this.elements.$data.attr('data-speed'), 
            coloumn = this.elements.$data.attr('data-column');   
        var swiper = new Swiper(selector, {
            slidesPerView: coloumn,
            spaceBetween: 12,
            slidesPerGroup: 1,
            autoplay: autply,
            loop: true,
            delay:3000,
            speed: Number(speeds),
            autoHeight: true,
            pagination: {
                el: ".sfafe-pagination.swiper-pagination",
                type: "fraction",
            },
            navigation: {
                nextEl: ".sfafe-next-btn.swiper-button-next",
                prevEl: ".sfafe-prev-btn.swiper-button-prev",
            },
            breakpoints: {
                // when window width is >= 320px
                100: {
                    slidesPerView: 1,

                },
                // when window width is >= 480px
                480: {
                    slidesPerView: 2,
                    spaceBetween: 10
                },
                // when window width is >= 640px
                640: {
                    slidesPerView: 2,
                    spaceBetween: 12
                },
                 900: {
                     slidesPerView: coloumn,
                    spaceBetween: 12
                }
            }
        });
        
    }


}

jQuery(window).on('elementor/frontend/init', () => {
    const addHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(Sfafe_custom, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/Instagram-Feed-Addon.default', addHandler);
});