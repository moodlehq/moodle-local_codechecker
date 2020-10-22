# Releasing a new version

This guide is to release a new version of the Code-checker plugin. Remember that when considering the version number to use, that
 this project follows [Semantic Versioning](http://semver.org/), so bump the version number accordingly.

Check the existing PRs and approve those that are applicable to this release.

Create a standard PR to get the version bump reviewed and incorporated upstream. Please **avoid merge commit** for the new
 release merge (use "Rebase and merge" option). Ensure the following have been updated:

* The `CHANGES.md`, adding the PRs that have been approved and other existing changes.
* The `version.php` file, at least the *version* and *release* values.

Once the version bump PR has been reviewed and incorporated upstream, then you need to tag the release, that will trigger a
 Travis CI build to run the integration testing.

Tag `master` branch `HEAD` and push using commands:

```bash
$ git tag -a 2.9.8 -m "Release version 2.9.8"
$ git push origin 2.9.8
```

It's also possible to use the GitHub interface to create a new release and tag.

# Moodle plugins directory
Once the new release is ready, you should add the latest release to the `Moodle plugins directory`.
1. Login to the plugins' directory site and go to [plugin page](https://moodle.org/plugins/local_codechecker).
2. To add a new version, you have to be a plugin maintainer. **Only lead maintainers can add other maintainers**.
3. In the main action bar, follow the *</>Developer zone* option.
4. Click `Add a new version`.
5. Select the appropriate GitHub release from the drop-down and keep the default options. Add the supported Moodle versions and click `Continue`.
6. On the next page, fill in the required information. In the *Release notes* field we put the concatenation of the CHANGES.md last version section and the README.md, keeping the travis badge. Click `Save changes`.

That's pretty much it. The new version is publicly available.
