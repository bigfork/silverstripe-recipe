# SilverStripe Recipe

Bigfork’s quickstart recipe for simple SilverStripe 5 projects. Contains frequently used modules, templates, config settings, JavaScript and CSS.

## Project setup

- Run `composer create-project bigfork/silverstripe-recipe ./project dev-master`
- Answer yes to “Do you want to remove the existing VCS (.git, .svn..) history?”
- Run `ddev start`
- Draw the rest of the owl

## Deployments

We use [Deployer](https://deployer.org/) for deployments, which can be installed either globally (recommended):

```bash
curl -LO https://deployer.org/deployer.phar
mv deployer.phar /usr/local/bin/dep
chmod +x /usr/local/bin/dep
```

Or on a per-project basis with Composer:

```bash
composer --dev require deployer/deployer
```

### Configuration

Edit `deploy/config.php` and set the application name and git repository URL. Everything else is optional.

### Deploying a site

It’s as easy as `dep deploy`.

On the first deploy, you’ll probably want to include the database and assets:

```
dep deploy
dep silverstripe:upload_assets
dep silverstripe:upload_database
```

You’ll also be asked (the first time you deploy to a given stage) to provide database credentials used to populate `.env`.

#### Deploying to production

Much the same as deploying to staging, just provide a third argument to select the stage (either `staging` or `production`):

```
dep deploy production
```

#### Deploy a branch/tag

```
# Deploy the dev branch to staging
dep deploy --branch=dev

# Deploy tag 1.0.1 to production
dep deploy production --tag=1.0.1
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

# Upload assets to production
dep silverstripe:upload_assets production

# Upload database to production
dep silverstripe:upload_database production

# Download assets from production
dep silverstripe:download_assets production

# Download database from production
dep silverstripe:download_database production
```

#### Manual dev/build

```
# dev/build on staging
dep silverstripe:dev_build

# dev/build on production
dep silverstripe:dev_build production
```
