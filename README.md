# SilverStripe Recipe

Bigfork’s quickstart recipe for simple SilverStripe 4 projects. Contains frequently used modules, templates, config settings, JavaScript and CSS.

## Project setup

- Run `composer create-project bigfork/silverstripe-recipe ./project dev-master`
- Answer yes to “Do you want to remove the existing VCS (.git, .svn..) history?”
- Enter database host and name
- Point your vhost document root to `/project/public`

## Deployments

We use [Deployer](https://deployer.org/) for deployments, which can be installed either globally (recommended):

```bash
curl -LO https://deployer.org/deployer.phar
mv deployer.phar /usr/local/bin/dep
chmod +x /usr/local/bin/dep
```

Or on a per-project basis with Composer:

```bash
composer require deployer/deployer
```

In order to upload/download assets and databases, you’ll also need to install `sspak`:

```bash
curl -sS https://silverstripe.github.io/sspak/install | php -- /usr/local/bin
```

### Configuration

Edit `deploy/config.php` and set the application name and git repository URL. Everything else is optional.

### Deploying a site

It’s as easy as `dep deploy`.

On the first deploy, you’ll probably want to include the database and assets:

```
dep deploy --include-assets --include-db
```

You’ll also be asked (the first time you deploy to a given stage) to provide database credentials used to populate `.env`.

#### Deploying to staging

Much the same as deploying to live, just provide a third argument (either `stage` or `prod`):

```
dep deploy stage --include-assets --include-db
```

#### Deploy a branch/tag

```
# Deploy the dev branch
dep deploy --branch=dev

# Deploy tag 1.0.1
dep deploy stage --tag=1.0.1
```

#### Uploading/downloading database & assets manually

```
# Upload assets
dep silverstripe:upload_assets

# Upload database
dep silverstripe:upload_database

# Download assets
dep silverstripe:download_assets

# Download database
dep silverstripe:download_database

# Upload assets to staging
dep silverstripe:upload_assets stage

# Upload database to staging
dep silverstripe:upload_database stage

# Download assets from staging
dep silverstripe:download_assets stage

# Download database from staging
dep silverstripe:download_database stage
```

#### Manual dev/build

```
# dev/build on production
dep silverstripe:dev_build

# dev/build on staging
dep silverstripe:dev_build stage
```
