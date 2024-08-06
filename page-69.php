<?php
get_header();
getBanner();
?>
<h1 style="text-align: center;">Filter API</h1>
<div class="container container-narrow page-section">
    <div class="generic-content">
        <div class="row group">
            <div class="one-third">
                <select name="" id="select_sort" onclick="myFunction(event)">
                    <option selected value="ALL" id="all">--Sắp xếp--</option>
                    <option value="asc">Cũ nhất</option>
                    <option value="desc">Mới nhất</option>
                    <option value="desc">Z-A</option>
                    <option value="asc">A-Z</option>
                </select>
            </div>
            <div class="two-thirds">
                <ul id="professorCards">

                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    all.click();
    function myFunction(e) {
        const giatri = document.getElementById("select_sort").value;
        const type = select_sort.options[select_sort.selectedIndex].innerHTML;
        console.log(type);
        let kq = [];
        switch (giatri) {
            case "desc":
                if (type == "Mới nhất") {
                    let results = fetch(
                            "https://pegasus.edu.vn/wp-json/wp/v2/posts?orderby=id&order=desc"
                        )
                        .then((response) => response.json())
                        .then((data) => {
                            kq = data;
                            inKetqua(kq);
                        })
                } else {
                    let results = fetch(
                            "https://pegasus.edu.vn/wp-json/wp/v2/posts?orderby=title&order=desc"
                        )
                        .then((response) => response.json())
                        .then((data) => {
                            kq = data;
                            inKetqua(kq);
                        });
                }
                break;

            case "asc":
                if (type == "Cũ nhất") {
                    let results1 = fetch(
                            "https://pegasus.edu.vn/wp-json/wp/v2/posts?orderby=id&order=asc"
                        )
                        .then((response) => response.json())
                        .then((data) => {
                            kq = data;
                            inKetqua(kq);
                        });

                } else {
                    let results1 = fetch(
                            "https://pegasus.edu.vn/wp-json/wp/v2/posts?orderby=title&order=asc"
                        )
                        .then((response) => response.json())
                        .then((data) => {
                            kq = data;
                            inKetqua(kq);
                        });

                }
                break;
            default:
                let total = fetch("https://pegasus.edu.vn/wp-json/wp/v2/posts").then((response) => response.json()).then((data) => {
                    kq = data;
                    inKetqua(kq);
                })
        }
    }


    function inKetqua(data) {
            const items = data.map((item) => `
                         <li class="professor-card__list-item">
                            <a class="professor-card" href="${item.link}">
                                <img class="professor-card__image" src="${item.yoast_head_json.og_image[0].url}" alt="">
                              <span class="professor-card__name">${item.title.rendered.slice(0,55)}</span>
                            </a>
                          </li>
                    `).join('');
            document.getElementById('professorCards').innerHTML = items;
      }
</script>
<?php
get_footer();
?>