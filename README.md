# dev-blog-api

A blogging app using API Platform and ReactJS. This project is still a pre-production release.

## Getting Started

This is the API resource for the [ReactJS Client](https://github.com/collierscott/dev-blog-client).

1. Clone the repository into a directory and cd into that directory:
```git clone https://github.com/collierscott/dev-blog-api.git && cd dev-blog-api```

2. Run ```composer install```

3. Create the ```.env``` file and configure to connect to your database.

4. If the database is not created, run ```php bin/console doctrine:database:create``` to create it.

5. Can run ```php bin/console doctrine:migrates:migrate``` to create the database structure.

## Data Fixtures

Run ```php bin/console doctrine:fixtures:load``` to load data fixtures.

## Testing 

There are some Behat tests. This area is lacking. But, should be enough to provide examples that can be used to write more tests.
