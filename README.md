# Resource Management

The task from the remote company.

### Requirements:
1. The application should be in Laravel in the Backend and Vue.js in the fronted.
2. The application should have the route `/admin` for the admin to manage resources.
3. The application should have the route `/` for the visitors to view the resources.
4. The admin url should provide the interface to the admin user to create/delete/update the resource available.
5. The admin interface should be in `Vue.js` embedded within the `blade` file. 
6. There should be three types of resources initially
   1. HTML Resource
      - The admin should be able to add title, description and snippet for html resource.
      - The visitor should be able to view the html resource, copy the snippet.
   2. PDF Resource
      - The admin should be able to add title and pdf file to add pdf resource.
      - The visitor should be able to download the pdf resource.
   3. Link Resource
      - The admin should be able to add title and link for the link the resource.
      - The visitor should be able to open the link in the new/same window/tab.


## Preparing the solution:

1. Prepare database and the required tables which can persist resources.
   - To facilitate the admin to store the records there exists a `resources` table in the system. Its schema has a `id(primary key)`
`title(string)` and `properties(json)`. The title would hold the resource type be it `pdf`,`html`, or `link`. The `json` column
`properties` would hold the attributes of the respective resources.

2. Prepare api/web end-points:
   1. API Endpoints
      1. POST - `/api/admin/resources` 
         - This allows the admin to add a resource to the system with proper validation.
      2. PUT- `/api/admin/resources/{resource}`
         - This allows the admin to update an existing resource and provides proper validation.
      3. DELETE - `/api/admin/resources/{resource}`
         - This allows the admin to remove the resource from the system.
   
   2. Web Endpoints
      1. GET - `/`
         - As the requirement asks to provide the endpoint for visitor, the application provides the url to view the resources for the 
      visitor.
      2. GET - `/admin`
         - As the requirement asks to provide the endpoint for the admin, the application provides the url to manage the resources.

## Usage of the application.

- The app opens up with the visitor's page which shows the list of the resources available. The visitor can perform the 
actions needed i.e. view html or download pdf or open the link from the list page.
- The admin can access the admin page which also shows them the list of added resources. 
- The admin can click `Create Resource` button to add a resource. 
- Afterwards, they would be shown a modal box which would allow to select their preferred resource and add the metadata required for the 
resource.
- The system tries to validate the input and show validation errors/messages and if there were no such errors, the application adds the resource to the system.
- The admin can delete a specific resource. Deleting a resource will remove the record from the database and in the case of pdf the pdf file also
banishes from the system.
- The admin can select to update a specific resource from the list with the Edit button.
- The edit button presents the admin with the modal box to update the fields of a resource.
- The admin however, cannot update the resource type from `pdf` to `html` or vice versa. 
- For pdf resources, the system allows the admin to allow only update the pdf title if they do not wish to update the pdf.

## Architecture
- MVC (Model View Controller)

## Frameworks/Libraries/Tools:
- [Laravel (v.9)](https://laravel.com)
- [Vue.js (v.2)](http://vuejs.org)
- [VueToastNotification](https://github.com/ankurk91/vue-toast-notification)
- [VueModal](https://github.com/euvl/vue-js-modal)
- [Bootstrap](https://getbootstrap.com/docs/5.0/getting-started/introduction/)

## Installation

Run the following commands to set up the application, given that `docker` is available on the machine:

1. `git clone https://github.com/ravisharmaa/resources-app.git`
2. `cd resources-app`
3. `docker-compose build`
4. `docker-compse up -d`
5. `docker exec -it resources-app /bin/bash`
6. `composer install`
7. `cp .env.example .env` 
8. `php artisan key:generate`
9. Run `php artisan migrate`
10. Run `php artisan storage:link` 
11. On the browser please browse `http://localhost:8083` to visit the app.

## Running Tests
1. `vendor/bin/phpunit`
2. For code coverage:  `vendor/bin/phpunit --coverage-text`
3. A  test report can also be generated using the command `vendor/bin/phpunit --coverage-html=reports`

## Decisions, tradeoffs and constraints

1. I chose to add the `properties` as `json` column to store the resources meta-data. This brings up the concern of not being able to
index the column which might lead to performance issues. However, it gives the flexibility to add more metadata for a resource
in future if needed. Adding nullable fields per resources would not be a good approach if we wished to scale this system. But given the spectrum of test assignment,  I believe it should not affect.
2. At the moment of doing the task, a new Laravel version came up with a new technique of bundling assets. I knowingly
omitted it, due to the learning curve associated.
   
## Future Improvements.

It was a challenge. However, if I had to improve upon this:

1. I did not use an extra controller to serve the views. I used the `routes/web.php` for the sake of this task. If it were to grow I would refactor it would refactor it to a proper controller
2. I used the boilerplate architecture, if it were a big project I would consider a different approach of using DDD(Domain Driven Design) or something like.
3. Pagination support for list pages
4. Upgrade laravel mix and use vite to bundle resources

# Docker Issues (Might not come for others)
1. Docker desktop sometimes errors out on could not fetch image restarting docker desktop helped and also even the nginx.conf file is present
docker says it not to be mounted, on that case as well restart helped. 

# Known Bug/s
1. Firefox does not seem to download the pdf resource, but open in a new tab, whereas Safari/Chrome and download that.