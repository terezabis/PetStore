PetStore
========================
Web application - web store for sale animals.
Used PHP 7 (Symfony), HTML, CSS, Bootstrap, MySQL.

Parts in Application

Public part:
- accessible and visible without authentication;
- consists of home page (without greeting for user), sign in and sign up forms
- everyone may register for free or login in the site if has account
- home page contains all products (animals) ordered by "new->old" and "in stock->out of stock"
- everyone can view animal details page

Private part and User area:
- all logged in users have access to pages with all categories, all products by category
- each user may buy products
- each user has his own cart with products witch he added in
- logged in user may add and remove products from his cart
- user may finish his order - his cart become empty and than the animals in it will have status out of stock
- each logged in user can view all done orders by himself

Administration part:
- there is only one user with role "Admin"
- admin has private area for add new products and categories in the store
- admin can edit and delete products and create new ones
- admin can edit categories and create new ones
- admin can view list of all products made by every user registered in the store
- admin can delete order