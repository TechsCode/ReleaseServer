package me.TechsCode.ReleaseServer.objects;

public class Deployment {

    private boolean enabled;
    private Remote remote;
    private String path;
    private String[] commands;

    public Deployment(boolean enabled, Remote remote, String path, String[] commands) {
        this.enabled = enabled;
        this.remote = remote;
        this.path = path;
        this.commands = commands;
    }

    public boolean isEnabled() {
        return enabled;
    }

    public Remote getRemote() {
        return remote;
    }

    public String getPath() {
        return path;
    }

    public String[] getCommands() {
        return commands;
    }
}
