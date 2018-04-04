# CDR API Code

Real World provides this code as a reference point for customers who wisht o retrieve CDRs
programatically from our billing system when they are unable to use CAT to obtain their
CDR details.

This library is provided as PHP source and a Docker image. We recommend installing Docker
and running the code from within a Docker Environment. We are aware of some issues with
PHP on MacOS and other platforms that can cause failures in retrieval of CDR records.

You can download Docker for Mac or Windows at docker.com. We provide a docker-compose file
for convenience, and so you will also want to install compose. The specifics of this process
are outside the scope of this readme.

To use, assuming you are using a Unix style CLI, with compose installed.

```
git clone https://github.com/realworldtech/smile-cdr-api.git
cd smile-cdr-api
docker-compose build app
```

You need to provide your API username, passsword and Master USN (which is provided by
Real World) in the `env.env` file included as part of this code set.

```
cp env.env .env
```

Edit `.env` using your favourite text editor to enter the correct details.

Once you have done this, you can run the code set, which will by default retrieve
CDR records for the last month and place a CDR file for each username in a directory
inside the output directory.

To do this run:

```
docker-compose run --rm app
```

If you need a combined file for your billing system, you can create one after your
content download by running

```
docker-compose run --rm app -c
```

-------------

If you have comments or suggestions please contact support@rwts.com.au or submit a Pull
Request on github.com
