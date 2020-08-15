package me.TechsCode.ReleaseServer;

import java.io.File;

public class Artifact {

    private Release release;
    private File assetsFolder;

    public Artifact(Release release, File assetsFolder) {
        this.release = release;
        this.assetsFolder = assetsFolder;
    }

    public Release getRelease() {
        return release;
    }

    public File[] getAssets() {
        return assetsFolder.listFiles();
    }

    public String getReleaseTag(){
        return release.getUniqueTag();
    }
}

