import $ from 'jquery';
class Search {
    constructor() {
        // alert("duoc");
        this.resultDiv = $('#search-overlay__results');
        this.openSearch = $('.js-search-trigger');
        this.closeSearch = $('.search-overlay__close');
        this.overlay = $('.search-overlay');
        this.searchItem = $('#search-term'); 
        this.openOverlays = false;
        this.spinnerVisible = false;
        this.previousValue = '';
        this.timing;
        this.event();
    }

    event() {
        this.openSearch.on("click", this.openOverlay.bind(this));
        this.closeSearch.on("click", this.closeOverlay.bind(this));
        $(document).on("keydown", this.dispatchKeyPress.bind(this));
        this.searchItem.on("keyup", this.typingLogic.bind(this));
    }

    dispatchKeyPress(e) {
        if(e.keyCode == 83 && !this.openOverlays) this.openOverlay();
        if(e.keyCode == 27 && this.openOverlays) this.closeOverlay();
    }

    typingLogic() { 
        if(this.previousValue != this.searchItem.val()) {
            clearTimeout(this.timing);
            if(this.searchItem.val()) {
                if(!this.spinnerVisible) {
                    this.resultDiv.html('<div class="spinner-loader"></div>');
                    this.spinnerVisible = true;
                }
                this.timing = setTimeout(() => {
                    this.getResults();
                }, 2000);
            } 
            else {
                this.resultDiv.html('');
                this.spinnerVisible = false;
            }
        }
        
        this.previousValue = this.searchItem.val();
    }

    getResults() {
        $.getJSON(universityData.root_url + '/wp-json/university/v1/universities?term=' + this.searchItem.val(), results => {
            console.log(results);
            this.resultDiv.html(`
                <div class="row">
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">General Information</h2>
                        ${results.general_info.length ? '<ul class="link-list min-list">' : '<p>General Information no match with search</p>'}
                        ${results.general_info.map(item => `<li><a href='${item.permalink}'>${item.title} by ${item.authorName}</a></li>`).join('')}
                        ${results.general_info.length ? ' </ul>' : ''}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Programs</h2>
                        ${results.programmes.length ? '<ul class="link-list min-list">' : '<p>Programs no match with search</p>'}
                        ${results.programmes.map(item => `<li><a href='${item.permalink}'>${item.title} by ${item.authorName}</a></li>`).join('')}
                        ${results.programmes.length ? ' </ul>' : ''}
                        <h2 class="search-overlay__section-title">Professors</h2>
                        ${results.professors.length ? '<ul class="link-list min-list">' : '<p>Professors no match with search</p>'}
                        ${results.professors.map(item => `<li class="professor-card__list-item">
                        <a href="${item.permalink}" class="professor-card">
                            <img src="${item.image}" alt="" class="professor-card__image">
                            <span class="professor-card__name">${item.title}</span>
                        </a>
                        </li>`).join('')}
                        ${results.professors.length ? ' </ul>' : ''}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Events</h2>
                        ${results.events.length ? '<ul class="link-list min-list">' : '<p>Events no match with search</p>'}
                        ${results.events.map(item => `<div class="event-summary">
                            <a class="event-summary__date t-center" href="${item.permalink}">
                                <?php 
                                $eventsDate = new DateTime(get_field('events_date'));
                                ?>
                                <span class="event-summary__month">${item.date}</span>
                                <span class="event-summary__day">${item.month}</span>
                            </a>
                            <div class="event-summary__content">
                                <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>
                                <p>${item.description}<a href="${item.permalink}" class="nu gray">Read more</a></p>
                            </div>
                            </div>`).join('')}
                        ${results.events.length ? ' </ul>' : ''}
                    </div>
                </div>
                `)
                this.spinnerVisible = false;
        })
        // $.when(
        //     $.getJSON(universityData.root_url + '/wp-json/wp/v2/posts?search=' + this.searchItem.val()),
        //     $.getJSON(universityData.root_url + '/wp-json/wp/v2/pages?search=' + this.searchItem.val()), 
        // ).then((posts, pages) => {
        //     const combineResults = posts[0].concat(pages[0]);
        //     this.resultDiv.html(`
        //         <h2 class="search-overlay__section-title">General Information</h2>
        //         ${combineResults.length ? '<ul class="link-list min-list">' : '<p>General Information no match with search</p>'}
        //         ${combineResults.map(item => `<li><a href='${item.link}'>${item.title.rendered} by ${item.authorName}</a></li>`).join('')}
        //         ${combineResults.length ? ' </ul>' : ''}
        //         `)
        //     this.spinnerVisible = false;
        // }, () => {
        //     this.resultDiv.html("<p>Error in search</p>");
        // })
    }

    openOverlay() {
        this.overlay.addClass('search-overlay--active');
        this.openOverlays = true;
        $('body').addClass('body-no-scroll');
    }

    closeOverlay() {
        this.overlay.removeClass('search-overlay--active');
        this.openOverlays = false;
        $('body').removeClass('body-no-scroll');
    }
}

export default Search;